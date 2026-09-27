<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    /** Select products + how many labels each, before printing. */
    public function index()
    {
        $products = Product::orderBy("name")->get(["id","name","code","sale_price"]);
        $categories = Category::orderBy("name")->get();

        return view("admin.barcodes.index", compact("products", "categories"));
    }

    /** Renders a printable grid of barcode + QR labels for the selected products. */
    public function print(Request $request)
    {
        $request->validate([
            "product_ids" => "required|array",
            "product_ids.*" => "exists:products,id",
            "quantities" => "array",
        ]);

        $products = Product::whereIn("id", $request->product_ids)->get();
        $quantities = $request->quantities ?? [];

        $labels = [];
        foreach ($products as $p) {
            $qty = (int) ($quantities[$p->id] ?? 1);
            for ($i = 0; $i < max($qty, 1); $i++) {
                $labels[] = $p;
            }
        }

        return view("admin.barcodes.print", compact("labels"));
    }
}
