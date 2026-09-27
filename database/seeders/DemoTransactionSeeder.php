<?php
namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeds a handful of demo purchase & sale invoices ONLY if none exist locally yet,
 * so the dashboard charts and reports have something to display out of the box.
 * Uses the real batch/FEFO stock engine so figures stay consistent with the rest
 * of the system (Product::stock_qty, stock_movements, product_batches all agree).
 */
class DemoTransactionSeeder extends Seeder
{
    public function run(): void
    {
        if (Purchase::count() > 0 || Sale::count() > 0) {
            return; // real/local data already present — do not touch it
        }

        $admin = User::where("email", "admin@pharmacy.test")->first();
        $branch = Branch::main();
        $supplier = Supplier::first();
        $products = Product::inRandomOrder()->limit(5)->get();

        if (!$branch || $products->isEmpty()) return;

        foreach (range(1, 6) as $i) {
            $date = now()->subDays(rand(1, 300));
            $items = $products->random(min(3, $products->count()));
            $total = 0;

            $purchase = Purchase::create([
                "invoice_no" => "PUR-" . strtoupper(Str::random(8)),
                "branch_id" => $branch->id,
                "supplier_id" => $supplier?->id,
                "purchase_date" => $date,
                "total_amount" => 0, "discount" => 0, "tax" => 0, "shipping_cost" => 0,
                "grand_total" => 0, "paid_amount" => 0, "due_amount" => 0,
                "payment_status" => "paid", "status" => "completed", "created_by" => $admin?->id,
            ]);

            foreach ($items as $product) {
                $qty = rand(10, 50);
                $subtotal = $qty * $product->purchase_price;
                $total += $subtotal;
                $batchNo = ProductBatch::generateBatchNo($product->code);

                $purchaseItem = $purchase->items()->create([
                    "product_id" => $product->id, "batch_no" => $batchNo, "quantity" => $qty,
                    "purchase_price" => $product->purchase_price, "sale_price" => $product->sale_price,
                    "expiry_date" => now()->addMonths(rand(1, 18)), "subtotal" => $subtotal,
                ]);

                $product->receiveBatch(
                    branchId: $branch->id, qty: $qty, purchasePrice: $product->purchase_price,
                    salePrice: $product->sale_price, batchNo: $batchNo,
                    expiryDate: $purchaseItem->expiry_date, reference: $purchaseItem, note: "Demo purchase seed"
                );
            }
            $purchase->update(["total_amount" => $total, "grand_total" => $total, "paid_amount" => $total]);
        }

        foreach (range(1, 20) as $i) {
            $date = now()->subDays(rand(0, 300));
            $items = $products->random(min(3, $products->count()));
            $total = 0; $profit = 0;

            $sale = Sale::create([
                "invoice_no" => "INV-" . strtoupper(Str::random(8)),
                "branch_id" => $branch->id,
                "channel" => "pos",
                "order_status" => "completed",
                "customer_id" => null,
                "sale_date" => $date,
                "total_amount" => 0, "discount" => 0, "tax" => 0, "grand_total" => 0,
                "paid_amount" => 0, "due_amount" => 0, "profit" => 0,
                "payment_method" => "cash", "payment_status" => "paid", "status" => "completed",
                "created_by" => $admin?->id,
            ]);

            foreach ($items as $product) {
                $available = $product->stockAtBranch($branch->id);
                $qty = min(rand(1, 8), max($available, 0));
                if ($qty < 1) continue;

                $subtotal = $qty * $product->sale_price;
                $total += $subtotal;
                $profit += $subtotal - ($qty * $product->purchase_price);

                $allocations = $product->issueStock($branch->id, $qty, "sale", $sale, "Demo sale seed");

                $sale->items()->create([
                    "product_id" => $product->id, "quantity" => $qty,
                    "sale_price" => $product->sale_price, "purchase_price" => $product->purchase_price,
                    "subtotal" => $subtotal, "batch_allocations" => $allocations,
                ]);
            }
            $sale->update(["total_amount" => $total, "grand_total" => $total, "paid_amount" => $total, "profit" => $profit]);
        }
    }
}
