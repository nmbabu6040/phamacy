<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;

class OrderTrackController extends Controller
{
    public function index()
    {
        return view("frontend.shop.track");
    }

    public function find(Request $request)
    {
        $request->validate([
            "invoice_no" => "required|string",
            "phone" => "required|string",
        ]);

        $sale = Sale::where("invoice_no", $request->invoice_no)
            ->where("customer_phone", $request->phone)
            ->where("channel", "online")
            ->with("items.product")
            ->first();

        if (!$sale) {
            return back()->with("error", "No matching order found. Please check your invoice number and phone.");
        }

        return view("frontend.shop.track", compact("sale"));
    }
}
