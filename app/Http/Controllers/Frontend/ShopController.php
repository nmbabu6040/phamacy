<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Generic;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::where("status", 1)
            ->with(["category","generic","brand"])
            ->when($request->search, fn ($q) => $q->where("name", "like", "%{$request->search}%")
                ->orWhere("code", "like", "%{$request->search}%"))
            ->when($request->category, fn ($q) => $q->whereHas("category", fn ($c) => $c->where("slug", $request->category)))
            ->when($request->generic, fn ($q) => $q->whereHas("generic", fn ($g) => $g->where("slug", $request->generic)))
            ->when($request->sort === "price_low", fn ($q) => $q->orderBy("sale_price"))
            ->when($request->sort === "price_high", fn ($q) => $q->orderByDesc("sale_price"))
            ->when(!$request->sort, fn ($q) => $q->latest())
            ->paginate(12)->withQueryString();

        $categories = Category::where("status", 1)->orderBy("name")->get();
        $generics = Generic::orderBy("name")->get();
        $brands = Brand::orderBy("name")->get();

        return view("frontend.shop.index", compact("products", "categories", "generics", "brands"));
    }

    public function show(Product $product)
    {
        $product->load("units.unit");

        $related = Product::where("category_id", $product->category_id)
            ->where("id", "!=", $product->id)->where("status", 1)->limit(4)->get();

        return view("frontend.shop.show", compact("product", "related"));
    }
}
