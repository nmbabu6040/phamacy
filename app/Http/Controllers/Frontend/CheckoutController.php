<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Sale;
use App\Notifications\OrderConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        [$items, $subtotal] = CartController::cartDetails();

        if (empty($items)) {
            return redirect()->route("cart.index")->with("error", "Your cart is empty.");
        }

        return view("frontend.shop.checkout", compact("items", "subtotal"));
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "phone" => "required|string|max:30",
            "email" => "nullable|email",
            "address" => "required|string",
            "payment_method" => "required|in:cash,card,mobile_banking,due",
        ]);

        [$items, $subtotal] = CartController::cartDetails();
        if (empty($items)) {
            return redirect()->route("cart.index")->with("error", "Your cart is empty.");
        }

        // Online orders are fulfilled from the main branch by default.
        $branch = Branch::main();
        if (!$branch) {
            return back()->with("error", "No branch is configured yet to fulfil this order. Please contact us directly.");
        }

        try {
            $sale = DB::transaction(function () use ($request, $items, $subtotal, $branch) {
                $shipping = 60; // flat shipping fee — adjust as needed
                $grandTotal = $subtotal + $shipping;

                $customer = Customer::firstOrCreate(
                    ["phone" => $request->phone],
                    ["name" => $request->name, "email" => $request->email, "address" => $request->address, "status" => 1]
                );

                $sale = Sale::create([
                    "invoice_no" => "ORD-" . strtoupper(Str::random(8)),
                    "branch_id" => $branch->id,
                    "channel" => "online",
                    "order_status" => "pending",
                    "customer_id" => $customer->id,
                    "customer_name" => $request->name,
                    "customer_phone" => $request->phone,
                    "customer_email" => $request->email,
                    "sale_date" => now()->toDateString(),
                    "total_amount" => $subtotal,
                    "discount" => 0,
                    "tax" => 0,
                    "shipping_address" => $request->address,
                    "shipping_cost" => $shipping,
                    "grand_total" => $grandTotal,
                    "paid_amount" => $request->payment_method === "due" ? 0 : $grandTotal,
                    "due_amount" => $request->payment_method === "due" ? $grandTotal : 0,
                    "profit" => 0,
                    "payment_method" => $request->payment_method,
                    "payment_status" => $request->payment_method === "due" ? "due" : "paid",
                    "status" => "completed",
                    "note" => "Online order via storefront checkout",
                ]);

                $profit = 0;
                foreach ($items as $row) {
                    $product = $row["product"];
                    $qty = $row["qty"];

                    $allocations = $product->issueStock($branch->id, $qty, "sale", $sale, "Online order {$sale->invoice_no}");

                    $sale->items()->create([
                        "product_id" => $product->id,
                        "quantity" => $qty,
                        "sale_price" => $product->sale_price,
                        "purchase_price" => $product->purchase_price,
                        "discount" => 0,
                        "subtotal" => $row["line_total"],
                        "batch_allocations" => $allocations,
                    ]);

                    $profit += $row["line_total"] - ($qty * $product->purchase_price);
                }
                $sale->update(["profit" => $profit]);

                return $sale;
            });
        } catch (\RuntimeException $e) {
            return back()->with("error", $e->getMessage());
        }

        session()->forget("cart");

        $routes = [];
        if ($sale->customer_email) $routes["mail"] = $sale->customer_email;
        if ($sale->customer_phone) $routes["sms"] = $sale->customer_phone;
        if ($routes) {
            Notification::route("mail", $routes["mail"] ?? null)
                ->route("sms", $routes["sms"] ?? null)
                ->notify(new OrderConfirmation($sale));
        }

        return redirect()->route("checkout.success", $sale);
    }

    public function success(Sale $sale)
    {
        abort_unless($sale->channel === "online", 404);
        $sale->load("items.product");

        return view("frontend.shop.order-success", compact("sale"));
    }
}
