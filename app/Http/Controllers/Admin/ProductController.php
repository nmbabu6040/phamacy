<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Generic;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(["category","generic","brand","unit"])
            ->when($request->search, fn ($q) => $q->where("name", "like", "%{$request->search}%")
                ->orWhere("code", "like", "%{$request->search}%"))
            ->when($request->category_id, fn ($q) => $q->where("category_id", $request->category_id))
            ->when($request->stock === "low", fn ($q) => $q->whereColumn("stock_qty", "<=", "alert_qty"))
            ->latest()->paginate(15)->withQueryString();

        $categories = Category::orderBy("name")->get();

        return view("admin.products.index", compact("products", "categories"));
    }

    public function create()
    {
        return view("admin.products.create", [
            "categories" => Category::orderBy("name")->get(),
            "generics" => Generic::orderBy("name")->get(),
            "brands" => Brand::orderBy("name")->get(),
            "units" => Unit::orderBy("name")->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data["slug"] = Str::slug($data["name"]) . "-" . Str::random(5);

        if ($request->hasFile("image")) {
            $data["image"] = $request->file("image")->store("products", "public");
        }

        $openingQty = (int) ($data["stock_qty"] ?? 0);
        $data["stock_qty"] = 0; // will be recalculated from the opening batch below
        $product = Product::create($data);

        $this->saveUnits($request, $product);

        if ($openingQty > 0) {
            $branchId = app("current_branch")?->id ?? \App\Models\Branch::main()?->id;
            if ($branchId) {
                $product->receiveBatch(
                    branchId: $branchId,
                    qty: $openingQty,
                    purchasePrice: $product->purchase_price,
                    salePrice: $product->sale_price,
                    batchNo: "OPEN-" . strtoupper(\Illuminate\Support\Str::random(6)),
                    expiryDate: $product->expiry_date,
                    note: "Opening stock"
                );
            }
        }

        ActivityLog::record("created", "Product", "Created product: {$product->name}", $product);

        return redirect()->route("admin.products.index")->with("success", "Product created successfully.");
    }

    public function edit(Product $product)
    {
        $product->load("units.unit");

        return view("admin.products.edit", [
            "product" => $product,
            "categories" => Category::orderBy("name")->get(),
            "generics" => Generic::orderBy("name")->get(),
            "brands" => Brand::orderBy("name")->get(),
            "units" => Unit::orderBy("name")->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validated($request, $product->id);

        if ($request->hasFile("image")) {
            $data["image"] = $request->file("image")->store("products", "public");
        }

        // stock_qty is a cached aggregate driven by batches — never let the edit form overwrite it directly
        unset($data["stock_qty"]);
        $product->update($data);

        $this->saveUnits($request, $product);

        ActivityLog::record("updated", "Product", "Updated product: {$product->name}", $product);

        return redirect()->route("admin.products.index")->with("success", "Product updated successfully.");
    }

    /**
     * Persists the repeatable "Selling Units & Pricing" rows (Piece / Strip / Box / Bottle, each
     * with its own conversion factor + purchase/sale price), then mirrors the base unit's price
     * back onto products.unit_id / purchase_price / sale_price for the rest of the system to read.
     */
    private function saveUnits(Request $request, Product $product): void
    {
        $rows = $request->input("units", []);
        if (empty($rows)) return;

        $keepIds = [];
        $hasBase = false;

        foreach ($rows as $row) {
            if (empty($row["unit_id"])) continue;

            $isBase = !$hasBase && !empty($row["is_base_unit"]); // only the first checked row wins as base
            if ($isBase) $hasBase = true;

            $unit = $product->units()->updateOrCreate(
                ["unit_id" => $row["unit_id"]],
                [
                    "conversion_factor" => max((int) ($row["conversion_factor"] ?? 1), 1),
                    "purchase_price" => $row["purchase_price"] ?? 0,
                    "sale_price" => $row["sale_price"] ?? 0,
                    "is_base_unit" => $isBase,
                    "barcode" => $row["barcode"] ?? null,
                ]
            );
            $keepIds[] = $unit->id;
        }

        // If nothing was explicitly marked base, the smallest conversion factor wins by default
        if (!$hasBase) {
            $smallest = $product->units()->orderBy("conversion_factor")->first();
            if ($smallest) {
                $product->units()->where("id", "!=", $smallest->id)->update(["is_base_unit" => false]);
                $smallest->update(["is_base_unit" => true, "conversion_factor" => 1]);
            }
        }

        $product->units()->whereNotIn("id", $keepIds)->delete();
        $product->refresh();
        $product->syncBaseUnitMirror();
    }

    public function destroy(Product $product)
    {
        $name = $product->name;
        $product->delete();

        ActivityLog::record("deleted", "Product", "Deleted product: {$name}");

        return back()->with("success", "Product deleted successfully.");
    }

    /** Manual stock adjustment (in/out) from the product list. */
    public function adjustStock(Request $request, Product $product)
    {
        $request->validate([
            "quantity" => "required|integer|min:1",
            "direction" => "required|in:in,out",
            "note" => "nullable|string|max:255",
        ]);

        $qty = $request->direction === "in" ? (int) $request->quantity : -1 * (int) $request->quantity;
        $branchId = app("current_branch")?->id ?? \App\Models\Branch::main()?->id;
        $product->adjustStock($qty, "adjustment", null, $request->note ?? "Manual stock adjustment", $branchId);

        ActivityLog::record("stock_adjust", "Product", "Adjusted stock for {$product->name} by {$qty}", $product);

        return back()->with("success", "Stock updated successfully.");
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            "name" => "required|string|max:255",
            "code" => "required|string|max:100|unique:products,code" . ($ignoreId ? ",{$ignoreId}" : ""),
            "category_id" => "nullable|exists:categories,id",
            "generic_id" => "nullable|exists:generics,id",
            "brand_id" => "nullable|exists:brands,id",
            "unit_id" => "nullable|exists:units,id",
            "strength" => "nullable|string|max:50",
            "dosage_form" => "required|in:Tablet,Capsule,Syrup,Injection,Cream,Drops,Inhaler,Other",
            "purchase_price" => "required|numeric|min:0",
            "sale_price" => "required|numeric|min:0",
            "discount" => "nullable|numeric|min:0",
            "tax_percent" => "nullable|numeric|min:0",
            "stock_qty" => "required|integer|min:0",
            "alert_qty" => "required|integer|min:0",
            "expiry_date" => "nullable|date",
            "image" => "nullable|image|max:2048",
            "description" => "nullable|string",
            "status" => "boolean",
            "is_featured" => "boolean",
        ]);
    }
}
