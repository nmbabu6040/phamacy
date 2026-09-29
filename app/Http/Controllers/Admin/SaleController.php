<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Sale;
use App\Notifications\LowStockAlert;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $sales = Sale::with("customer", "branch")
            ->where("channel", "pos")
            ->when($request->search, fn($q) => $q->where("invoice_no", "like", "%{$request->search}%"))
            ->when($request->from, fn($q) => $q->whereDate("sale_date", ">=", $request->from))
            ->when($request->to, fn($q) => $q->whereDate("sale_date", "<=", $request->to))
            ->latest()->paginate(15)->withQueryString();

        return view("admin.sales.index", compact("sales"));
    }

    /** POS screen: search products live via AJAX, cart is built client-side then POSTed here. */
    public function pos()
    {
        return view("admin.sales.pos", [
            "customers" => Customer::orderBy("name")->get(),
        ]);
    }

    /**
     * AJAX: live product search for the POS screen. Also matches an exact barcode/code scan
     * (a USB barcode scanner just types the code + Enter into the same search box).
     */
    public function posSearch(Request $request)
    {
        $term = $request->get("q", "");
        $branchId = app("current_branch")?->id;

        $products = Product::where("status", 1)
            ->where(fn($q) => $q->where("name", "like", "%{$term}%")
                ->orWhere("code", "like", "%{$term}%"))
            ->where("stock_qty", ">", 0)
            ->with("units.unit")
            ->limit(12)
            ->get(["id", "name", "code", "sale_price", "stock_qty", "dosage_form"]);

        // Attach branch-specific stock, and the sellable unit tiers (Piece/Strip/Box...) so the
        // cashier can pick which one to sell in — falls back to a virtual "Piece" tier if the
        // product has no explicit ProductUnit rows configured yet.
        $products->transform(function ($p) use ($branchId) {
            $p->branch_stock = $branchId ? $p->stockAtBranch($branchId) : $p->stock_qty;
            $p->unit_options = $p->units->count()
                ? $p->units->map(fn($u) => [
                    "id" => $u->id,
                    "name" => $u->unit->name ?? "Unit",
                    "factor" => $u->conversion_factor,
                    "sale_price" => $u->sale_price,
                ])
                : collect([["id" => null, "name" => "Piece", "factor" => 1, "sale_price" => $p->sale_price]]);
            return $p;
        });

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            "customer_id" => "nullable|exists:customers,id",
            "items" => "required|array|min:1",
            "items.*.product_id" => "required|exists:products,id",
            "items.*.product_unit_id" => "nullable|exists:product_units,id",
            "items.*.unit_qty" => "required|integer|min:1", // quantity in whichever unit was picked (Piece/Strip/Box)
            "items.*.sale_price" => "required|numeric|min:0",
            "items.*.discount" => "nullable|numeric|min:0",
            "discount" => "nullable|numeric|min:0",
            "tax" => "nullable|numeric|min:0",
            "paid_amount" => "nullable|numeric|min:0",
            "payment_method" => "required|in:cash,card,mobile_banking,due",
        ]);

        $branchId = app("current_branch")?->id ?? \App\Models\Branch::main()?->id;

        // Resolve each line's TRUE conversion factor server-side (never trust the client for this —
        // it's what stops someone from selling "1 Box" but only having the stock of a Piece deducted).
        $resolvedItems = collect($request->items)->map(function ($item) {
            $factor = 1;
            if (!empty($item["product_unit_id"])) {
                $productUnit = \App\Models\ProductUnit::find($item["product_unit_id"]);
                $factor = $productUnit?->conversion_factor ?? 1;
            }
            $item["base_quantity"] = (int) $item["unit_qty"] * $factor;
            return $item;
        });

        $sale = DB::transaction(function () use ($request, $branchId, $resolvedItems) {
            $total = 0;
            $profit = 0;

            // Lock rows to prevent overselling from concurrent POS terminals
            $products = Product::whereIn("id", $resolvedItems->pluck("product_id"))
                ->lockForUpdate()->get()->keyBy("id");

            foreach ($resolvedItems as $item) {
                $product = $products[$item["product_id"]];
                $available = $product->stockAtBranch($branchId);
                if ($available < $item["base_quantity"]) {
                    abort(422, "Insufficient stock for {$product->name} at this branch. Available: {$available} base unit(s).");
                }
                $lineSubtotal = ($item["unit_qty"] * $item["sale_price"]) - ($item["discount"] ?? 0);
                $total += $lineSubtotal;
                $profit += $lineSubtotal - ($item["base_quantity"] * $product->purchase_price);
            }

            $grandTotal = $total - ($request->discount ?? 0) + ($request->tax ?? 0);
            $paid = $request->paid_amount ?? $grandTotal;
            $due = max($grandTotal - $paid, 0);

            $sale = Sale::create([
                "invoice_no" => "INV-" . strtoupper(Str::random(8)),
                "branch_id" => $branchId,
                "channel" => "pos",
                "order_status" => "completed",
                "customer_id" => $request->customer_id,
                "sale_date" => now()->toDateString(),
                "total_amount" => $total,
                "discount" => $request->discount ?? 0,
                "tax" => $request->tax ?? 0,
                "grand_total" => $grandTotal,
                "paid_amount" => $paid,
                "due_amount" => $due,
                "profit" => $profit,
                "payment_method" => $request->payment_method,
                "payment_status" => $due <= 0 ? "paid" : ($paid > 0 ? "partial" : "due"),
                "status" => "completed",
                "created_by" => auth()->id(),
                "note" => $request->note,
            ]);

            foreach ($resolvedItems as $item) {
                $product = $products[$item["product_id"]];

                // FEFO stock-out: deduct from the soonest-expiring batches first, record which batches were used
                $allocations = $product->issueStock($branchId, $item["base_quantity"], "sale", $sale, "Stock out via sale {$sale->invoice_no}");

                $sale->items()->create([
                    "product_id" => $product->id,
                    "product_unit_id" => $item["product_unit_id"] ?? null,
                    "unit_qty" => $item["unit_qty"],
                    "quantity" => $item["base_quantity"],
                    "sale_price" => $item["sale_price"],
                    "purchase_price" => $product->purchase_price,
                    "discount" => $item["discount"] ?? 0,
                    "subtotal" => ($item["unit_qty"] * $item["sale_price"]) - ($item["discount"] ?? 0),
                    "batch_allocations" => $allocations,
                ]);

                $product->refresh();
                if ($product->isLowStock()) {
                    \Illuminate\Support\Facades\Notification::send(
                        \App\Models\User::whereHas("role", fn($q) => $q->whereIn("slug", ["admin", "manager"]))->get(),
                        new LowStockAlert($product)
                    );
                }
            }

            return $sale;
        });

        ActivityLog::record("created", "Sale", "Created sale {$sale->invoice_no}", $sale);

        return response()->json([
            "success" => true,
            "invoice_url" => route("admin.sales.show", $sale),
            "message" => "Sale completed. Stock updated.",
        ]);
    }

    public function show(Sale $sale)
    {
        $sale->load(["items.product", "customer", "creator", "branch"]);
        return view("admin.sales.show", compact("sale"));
    }

    /** Downloadable PDF invoice for a sale (POS or online order). */
    public function pdf(Sale $sale)
    {
        $sale->load(["items.product", "customer", "branch"]);
        $pdf = Pdf::loadView("admin.sales.invoice_pdf", compact("sale"))->setPaper("a5");

        return $pdf->download("invoice-{$sale->invoice_no}.pdf");
    }

    public function destroy(Sale $sale)
    {
        DB::transaction(function () use ($sale) {
            foreach ($sale->items as $item) {
                if (!$item->product) continue;

                // Credit stock back into the exact batches it was taken from, when possible.
                $allocations = $item->batch_allocations ?? [];
                $restored = 0;
                foreach ($allocations as $alloc) {
                    $batch = ProductBatch::find($alloc["batch_id"] ?? null);
                    if ($batch) {
                        $batch->increment("quantity", $alloc["qty"]);
                        $restored += $alloc["qty"];
                    }
                }
                $remaining = $item->quantity - $restored;
                if ($remaining > 0) {
                    // Batch no longer exists (e.g. deleted) — restore as a fresh no-expiry batch
                    $item->product->receiveBatch($sale->branch_id ?? \App\Models\Branch::main()?->id, $remaining, $item->purchase_price, $item->sale_price, null, null, null, $sale, "Restored from cancelled sale {$sale->invoice_no}");
                }
                $item->product->recalcStock();

                // Log this as a proper "sale_return" movement so it shows up on the Sales Return report,
                // regardless of whether the stock went back into an existing batch or a new one above.
                $item->product->stockMovements()->create([
                    "branch_id" => $sale->branch_id,
                    "type" => "sale_return",
                    "quantity" => $item->quantity,
                    "stock_after" => $item->product->stock_qty,
                    "reference_type" => \App\Models\Sale::class,
                    "reference_id" => $sale->id,
                    "created_by" => auth()->id(),
                    "note" => "Sale returned: {$sale->invoice_no}",
                ]);
            }
            $sale->update(["status" => "cancelled", "order_status" => "cancelled"]);
        });

        ActivityLog::record("cancelled", "Sale", "Cancelled sale {$sale->invoice_no} and restored stock");

        return back()->with("success", "Sale cancelled and stock restored.");
    }
}
