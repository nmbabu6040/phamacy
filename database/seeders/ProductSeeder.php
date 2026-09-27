<?php
namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Generic;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $mainBranch = Branch::main();
        $category = fn ($name) => Category::where("name", $name)->first()?->id;
        $generic = fn ($name) => Generic::where("name", $name)->first()?->id;
        $brand = fn ($name) => Brand::where("name", $name)->first()?->id;
        $unit = fn ($name) => Unit::where("name", $name)->first()?->id;

        // "strip" = tablets/pieces per strip, "box_strips" = strips per box. null on both = not sold in strips (e.g. syrup, sold only per Bottle).
        $products = [
            ["name" => "Napa 500mg",        "code" => "MED-1001", "category" => "Pain Relief",     "generic" => "Paracetamol", "brand" => "Square Pharma", "strength" => "500mg", "form" => "Tablet",    "pp" => 1.20, "sp" => 1.50, "stock" => 500, "alert" => 50, "strip" => 10, "box_strips" => 10],
            ["name" => "Amoxin 250mg",      "code" => "MED-1002", "category" => "Antibiotics",     "generic" => "Amoxicillin", "brand" => "Beximco",       "strength" => "250mg", "form" => "Capsule",   "pp" => 3.50, "sp" => 4.50, "stock" => 300, "alert" => 40, "strip" => 10, "box_strips" => 10],
            ["name" => "Brufen 400mg",      "code" => "MED-1003", "category" => "Pain Relief",     "generic" => "Ibuprofen",   "brand" => "Incepta",       "strength" => "400mg", "form" => "Tablet",    "pp" => 2.00, "sp" => 2.80, "stock" => 250, "alert" => 30, "strip" => 10, "box_strips" => 10],
            ["name" => "Seclo 20mg",        "code" => "MED-1004", "category" => "Diabetes Care",   "generic" => "Omeprazole",  "brand" => "Square Pharma", "strength" => "20mg",  "form" => "Capsule",   "pp" => 4.00, "sp" => 5.20, "stock" => 200, "alert" => 25, "strip" => 14, "box_strips" => 10],
            ["name" => "Alatrol 10mg",      "code" => "MED-1005", "category" => "Cold & Flu",      "generic" => "Cetirizine",  "brand" => "ACI",           "strength" => "10mg",  "form" => "Tablet",    "pp" => 0.80, "sp" => 1.20, "stock" => 400, "alert" => 50, "strip" => 10, "box_strips" => 10],
            ["name" => "Comet 500mg",       "code" => "MED-1006", "category" => "Diabetes Care",   "generic" => "Metformin",   "brand" => "Renata",        "strength" => "500mg", "form" => "Tablet",    "pp" => 1.50, "sp" => 2.00, "stock" => 350, "alert" => 40, "strip" => 10, "box_strips" => 10],
            ["name" => "Azithro 500",       "code" => "MED-1007", "category" => "Antibiotics",     "generic" => "Azithromycin","brand" => "Opsonin",       "strength" => "500mg", "form" => "Tablet",    "pp" => 8.00, "sp" => 10.50,"stock" => 150, "alert" => 20, "strip" => 5,  "box_strips" => 10],
            ["name" => "Losatan 50mg",      "code" => "MED-1008", "category" => "Cardiac Care",    "generic" => "Losartan",    "brand" => "Square Pharma", "strength" => "50mg",  "form" => "Tablet",    "pp" => 2.50, "sp" => 3.30, "stock" => 180, "alert" => 20, "strip" => 10, "box_strips" => 10],
            ["name" => "Atorva 10mg",       "code" => "MED-1009", "category" => "Cardiac Care",    "generic" => "Atorvastatin","brand" => "Beximco",       "strength" => "10mg",  "form" => "Tablet",    "pp" => 3.00, "sp" => 4.00, "stock" => 8,   "alert" => 20, "strip" => 10, "box_strips" => 10],
            ["name" => "Vitacee 500mg",     "code" => "MED-1010", "category" => "Vitamins & Supplements", "generic" => "Vitamin C", "brand" => "ACI",     "strength" => "500mg", "form" => "Tablet",    "pp" => 0.90, "sp" => 1.40, "stock" => 600, "alert" => 60, "strip" => 10, "box_strips" => 10],
            ["name" => "Alatrol Syrup 60ml","code" => "MED-1011", "category" => "Cold & Flu",      "generic" => "Cetirizine",  "brand" => "ACI",           "strength" => "60ml",  "form" => "Syrup",     "pp" => 35.00,"sp" => 48.00,"stock" => 80,  "alert" => 15, "strip" => null, "box_strips" => null],
        ];

        foreach ($products as $p) {
            $product = Product::firstOrCreate(
                ["code" => $p["code"]],
                [
                    "name" => $p["name"],
                    "slug" => Str::slug($p["name"]) . "-" . Str::random(4),
                    "category_id" => $category($p["category"]),
                    "generic_id" => $generic($p["generic"]),
                    "brand_id" => $brand($p["brand"]),
                    "unit_id" => $unit("Piece"),
                    "strength" => $p["strength"],
                    "dosage_form" => $p["form"],
                    "purchase_price" => $p["pp"],
                    "sale_price" => $p["sp"],
                    "stock_qty" => 0, // recalculated from the opening batch below
                    "alert_qty" => $p["alert"],
                    "status" => 1,
                    "is_featured" => rand(0, 1),
                ]
            );

            // Only give it an opening batch / unit pricing if it does not already exist locally.
            if ($mainBranch && $product->wasRecentlyCreated) {
                $product->receiveBatch(
                    branchId: $mainBranch->id,
                    qty: $p["stock"],
                    purchasePrice: $p["pp"],
                    salePrice: $p["sp"],
                    batchNo: ProductBatch::generateBatchNo($p["code"]),
                    expiryDate: now()->addMonths(rand(3, 24)),
                    note: "Opening stock (seeder)"
                );

                $this->seedUnitPricing($product, $p, $unit);
            }
        }
    }

    /**
     * Piece / Strip / Box tiers for tablets & capsules (with a small bulk discount baked into
     * each larger tier), or a single Bottle tier for syrups — demonstrates the multi-unit
     * pricing system (piece / strip / box / bottle rates) out of the box.
     */
    private function seedUnitPricing(Product $product, array $p, callable $unit): void
    {
        if (empty($p["strip"])) {
            // Sold only as a whole unit (e.g. syrup bottle) — one base tier, no strip/box breakdown.
            $product->units()->create([
                "unit_id" => $unit("Bottle"),
                "conversion_factor" => 1,
                "purchase_price" => $p["pp"],
                "sale_price" => $p["sp"],
                "is_base_unit" => true,
            ]);
            return;
        }

        $stripSize = $p["strip"];
        $boxSize = $stripSize * $p["box_strips"];

        $product->units()->create([
            "unit_id" => $unit("Piece"),
            "conversion_factor" => 1,
            "purchase_price" => $p["pp"],
            "sale_price" => $p["sp"],
            "is_base_unit" => true,
        ]);
        $product->units()->create([
            "unit_id" => $unit("Strip"),
            "conversion_factor" => $stripSize,
            "purchase_price" => round($p["pp"] * $stripSize * 0.97, 2),
            "sale_price" => round($p["sp"] * $stripSize * 0.97, 2), // ~3% cheaper per piece when buying a full strip
        ]);
        $product->units()->create([
            "unit_id" => $unit("Box"),
            "conversion_factor" => $boxSize,
            "purchase_price" => round($p["pp"] * $boxSize * 0.93, 2),
            "sale_price" => round($p["sp"] * $boxSize * 0.93, 2), // ~7% cheaper per piece when buying a full box
        ]);
    }
}
