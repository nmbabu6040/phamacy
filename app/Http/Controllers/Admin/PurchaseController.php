<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $purchases = Purchase::with("supplier")
            ->when($request->search, fn ($q) => $q->where("invoice_no", "like", "%{$request->search}%"))
            ->when($request->from, fn ($q) => $q->whereDate("purchase_date", ">=", $request->from))
            ->when($request->to, fn ($q) => $q->whereDate("purchase_date", "<=", $request->to))
            ->latest()->paginate(15)->withQueryString();

        return view("admin.purchases.index", compact("purchases"));
    }

    public function create()
    {
        return view("admin.purchases.create", [
            "suppliers" => Supplier::orderBy("name")->get(),
            "products" => Product::with("units.unit")->orderBy("name")->get(["id","name","code","purchase_price","sale_price"]),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            "supplier_id" => "nullable|exists:suppliers,id",
            "purchase_date" => "required|date",
            "items" => "required|array|min:1",
            "items.*.product_id" => "required|exists:products,id",
            "items.*.product_unit_id" => "nullable|exists:product_units,id",
            "items.*.unit_qty" => "nullable|integer|min:1",
            "items.*.quantity" => "required|integer|min:1", // always the BASE-unit (piece) total — computed client-side from unit_qty × conversion factor
            "items.*.purchase_price" => "required|numeric|min:0",
            "items.*.sale_price" => "nullable|numeric|min:0",
            "items.*.batch_no" => "nullable|string|max:100",
            "items.*.manufacturing_date" => "nullable|date",
            "items.*.expiry_date" => "nullable|date",
            "discount" => "nullable|numeric|min:0",
            "tax" => "nullable|numeric|min:0",
            "shipping_cost" => "nullable|numeric|min:0",
            "paid_amount" => "nullable|numeric|min:0",
        ]);

        $branchId = $request->branch_id ?: (app()->bound("current_branch") ? app("current_branch")?->id : null) ?: \App\Models\Branch::main()?->id;

        $purchase = DB::transaction(function () use ($request, $branchId) {
            $total = 0;
            foreach ($request->items as $item) {
                $total += $item["quantity"] * $item["purchase_price"];
            }
            $grandTotal = $total - ($request->discount ?? 0) + ($request->tax ?? 0) + ($request->shipping_cost ?? 0);
            $paid = $request->paid_amount ?? 0;
            $due = max($grandTotal - $paid, 0);

            $purchase = Purchase::create([
                "invoice_no" => "PUR-" . strtoupper(Str::random(8)),
                "branch_id" => $branchId,
                "supplier_id" => $request->supplier_id,
                "purchase_date" => $request->purchase_date,
                "total_amount" => $total,
                "discount" => $request->discount ?? 0,
                "tax" => $request->tax ?? 0,
                "shipping_cost" => $request->shipping_cost ?? 0,
                "grand_total" => $grandTotal,
                "paid_amount" => $paid,
                "due_amount" => $due,
                "payment_status" => $due <= 0 ? "paid" : ($paid > 0 ? "partial" : "due"),
                "status" => "completed",
                "created_by" => auth()->id(),
                "note" => $request->note,
            ]);

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item["product_id"]);
                $batchNo = $item["batch_no"] ?? \App\Models\ProductBatch::generateBatchNo($product->code);

                $purchaseItem = $purchase->items()->create([
                    "product_id" => $product->id,
                    "product_unit_id" => $item["product_unit_id"] ?? null,
                    "unit_qty" => $item["unit_qty"] ?? null,
                    "batch_no" => $batchNo,
                    "quantity" => $item["quantity"],
                    "purchase_price" => $item["purchase_price"],
                    "sale_price" => $item["sale_price"] ?? $product->sale_price,
                    "manufacturing_date" => $item["manufacturing_date"] ?? null,
                    "expiry_date" => $item["expiry_date"] ?? null,
                    "subtotal" => $item["quantity"] * $item["purchase_price"],
                ]);

                // Update product's default cost/sale price snapshot (used for the next purchase form)
                $product->purchase_price = $item["purchase_price"];
                if (!empty($item["sale_price"])) $product->sale_price = $item["sale_price"];
                $product->save();

                // Every unit received creates/extends a batch — this is what drives expiry & FEFO stock-out
                $product->receiveBatch(
                    branchId: $branchId,
                    qty: (int) $item["quantity"],
                    purchasePrice: $item["purchase_price"],
                    salePrice: $item["sale_price"] ?? $product->sale_price,
                    batchNo: $batchNo,
                    manufacturingDate: $item["manufacturing_date"] ?? null,
                    expiryDate: $item["expiry_date"] ?? null,
                    reference: $purchaseItem,
                    note: "Stock in via purchase {$purchase->invoice_no}"
                );
            }

            return $purchase;
        });

        ActivityLog::record("created", "Purchase", "Created purchase {$purchase->invoice_no}", $purchase);

        return redirect()->route("admin.purchases.show", $purchase)->with("success", "Purchase recorded and stock updated.");
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(["items.product", "supplier", "creator", "branch"]);
        return view("admin.purchases.show", compact("purchase"));
    }

    /** Downloadable PDF invoice for a purchase. */
    public function pdf(Purchase $purchase)
    {
        $purchase->load(["items.product", "supplier", "branch"]);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView("admin.purchases.invoice_pdf", compact("purchase"))->setPaper("a5");

        return $pdf->download("purchase-{$purchase->invoice_no}.pdf");
    }

    public function destroy(Purchase $purchase)
    {
        DB::transaction(function () use ($purchase) {
            foreach ($purchase->items as $item) {
                // Remove the exact batch this purchase item created (if any units remain) and log the reversal.
                $batch = \App\Models\ProductBatch::where("purchase_item_id", $item->id)->first();
                if ($batch) {
                    $sold = $batch->initial_quantity - $batch->quantity;
                    $batch->delete();
                    if ($item->product) {
                        $item->product->stockMovements()->create([
                            "branch_id" => $purchase->branch_id,
                            "type" => "adjustment",
                            "quantity" => -1 * $batch->initial_quantity,
                            "stock_after" => $item->product->stock_qty,
                            "reference_type" => Purchase::class,
                            "reference_id" => $purchase->id,
                            "created_by" => auth()->id(),
                            "note" => "Reversed purchase {$purchase->invoice_no}" . ($sold > 0 ? " ({$sold} units of this batch were already sold)" : ""),
                        ]);
                        $item->product->recalcStock();
                    }
                }
            }
            $purchase->delete();
        });

        ActivityLog::record("deleted", "Purchase", "Deleted purchase {$purchase->invoice_no}");

        return redirect()->route("admin.purchases.index")->with("success", "Purchase deleted and its stock batches reversed.");
    }
}
