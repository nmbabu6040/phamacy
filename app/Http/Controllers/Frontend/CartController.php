<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

/**
 * Guest cart stored entirely in the session — no customer account required.
 * Session shape: cart => [product_id => quantity]
 */
class CartController extends Controller
{
    public function index()
    {
        [$items, $subtotal] = $this->cartDetails();
        return view("frontend.shop.cart", compact("items", "subtotal"));
    }

    public function add(Request $request, Product $product)
    {
        $qty = max((int) $request->get("quantity", 1), 1);
        $cart = session("cart", []);
        $cart[$product->id] = ($cart[$product->id] ?? 0) + $qty;
        session(["cart" => $cart]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(["success" => true, "cart_count" => array_sum(session("cart", []))]);
        }

        return back()->with("success", "{$product->name} added to cart.");
    }

    public function update(Request $request, Product $product)
    {
        $qty = max((int) $request->get("quantity", 1), 0);
        $cart = session("cart", []);

        if ($qty === 0) {
            unset($cart[$product->id]);
        } else {
            $cart[$product->id] = $qty;
        }
        session(["cart" => $cart]);

        return back()->with("success", "Cart updated.");
    }

    public function remove(Product $product)
    {
        $cart = session("cart", []);
        unset($cart[$product->id]);
        session(["cart" => $cart]);

        return back()->with("success", "Item removed from cart.");
    }

    /** Shared helper: resolves session cart into [product => qty] rows + subtotal. Used by cart & checkout views. */
    public static function cartDetails(): array
    {
        $cart = session("cart", []);
        if (empty($cart)) return [[], 0];

        $products = Product::whereIn("id", array_keys($cart))->get()->keyBy("id");
        $items = [];
        $subtotal = 0;

        foreach ($cart as $productId => $qty) {
            $product = $products[$productId] ?? null;
            if (!$product) continue;
            $lineTotal = $product->sale_price * $qty;
            $subtotal += $lineTotal;
            $items[] = ["product" => $product, "qty" => $qty, "line_total" => $lineTotal];
        }

        return [$items, $subtotal];
    }
}
