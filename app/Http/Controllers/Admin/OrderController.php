<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Sale;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /** Online orders placed through the storefront checkout (channel = online). */
    public function index(Request $request)
    {
        $orders = Sale::with("branch")
            ->where("channel", "online")
            ->when($request->status, fn ($q) => $q->where("order_status", $request->status))
            ->when($request->search, fn ($q) => $q->where("invoice_no", "like", "%{$request->search}%")
                ->orWhere("customer_phone", "like", "%{$request->search}%"))
            ->latest()->paginate(15)->withQueryString();

        return view("admin.orders.index", compact("orders"));
    }

    public function show(Sale $sale)
    {
        $sale->load(["items.product", "branch"]);
        return view("admin.orders.show", compact("sale"));
    }

    public function updateStatus(Request $request, Sale $sale)
    {
        $request->validate(["order_status" => "required|in:pending,processing,shipped,delivered,cancelled"]);

        $sale->update(["order_status" => $request->order_status]);
        ActivityLog::record("updated", "Order", "Order {$sale->invoice_no} status changed to {$request->order_status}", $sale);

        return back()->with("success", "Order status updated.");
    }
}
