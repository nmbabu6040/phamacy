<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller

{
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfYear = Carbon::now()->startOfYear();


        // ---- Stat cards ----
        $todaySales = Sale::whereDate("sale_date", $today)->sum("grand_total");
        $monthSales = Sale::whereBetween("sale_date", [$startOfMonth, Carbon::now()])->sum("grand_total");
        $yearSales = Sale::whereBetween("sale_date", [$startOfYear, Carbon::now()])->sum("grand_total");
        $todayPurchase = Purchase::whereDate("purchase_date", $today)->sum("grand_total");
        $monthPurchase = Purchase::whereBetween("purchase_date", [$startOfMonth, Carbon::now()])->sum("grand_total");
        $monthExpense = Expense::whereBetween("expense_date", [$startOfMonth, Carbon::now()])->sum("amount");
        $monthProfit = Sale::whereBetween("sale_date", [$startOfMonth, Carbon::now()])->sum("profit") - $monthExpense;
        $totalProducts = Product::count();
        $lowStockCount = Product::whereColumn("stock_qty", "<=", "alert_qty")->count();
        $expiringSoonCount = Product::whereNotNull("expiry_date")->whereBetween("expiry_date", [$today, $today->copy()->addDays(90)])->count();
        $totalDue = Sale::sum("due_amount");

        // ---- Sales trend: last 12 months (for line chart) ----

        $salesTrend = Sale::select(DB::raw("DATE_FORMAT(sale_date, \"%Y-%m\") as ym"), DB::raw("SUM(grand_total) as total"), DB::raw("SUM(profit) as profit"))->where("sale_date", ">=", Carbon::now()->subMonths(11)->startOfMonth())->groupBy("ym")->orderBy("ym")->get();

        // ---- Sales vs Purchase last 7 days (bar chart) ----
        $last7Days = collect(range(6, 0))->map(fn($i) => Carbon::today()->subDays($i));
        $dailySales = $last7Days->map(fn($d) => Sale::whereDate("sale_date", $d)->sum("grand_total"));
        $dailyPurchase = $last7Days->map(fn($d) => Purchase::whereDate("purchase_date", $d)->sum("grand_total"));

        // ---- Top selling products (doughnut) ----
        $topProducts = SaleItem::select("product_id", DB::raw("SUM(quantity) as qty"))->groupBy("product_id")->orderByDesc("qty")->limit(5)->with("product:id,name")->get();

        // ---- Category-wise stock value (pie) ----
        $categoryStock = Product::select("category_id", DB::raw("SUM(stock_qty * purchase_price) as value"))->groupBy("category_id")->with("category:id,name")->get();
        $recentSales = Sale::with("customer")->latest()->limit(8)->get();
        $lowStockProducts = Product::whereColumn("stock_qty", "<=", "alert_qty")->orderBy("stock_qty")->limit(8)->get();
        $expiringProducts = Product::whereNotNull("expiry_date")->whereBetween("expiry_date", [$today, $today->copy()->addDays(90)])->orderBy("expiry_date")->limit(8)->get();

        // ---- Recent Contact Messages ----
        $recentContactMessages = ContactMessage::latest()
            ->limit(5)
            ->get();

        // ---- Recent Newsletter Subscribers ----
        $recentSubscribers = NewsletterSubscriber::latest()
            ->limit(5)
            ->get();

        return view("admin.dashboard.index", compact("todaySales", "monthSales", "yearSales", "todayPurchase", "monthPurchase", "monthExpense", "monthProfit", "totalProducts", "lowStockCount", "expiringSoonCount", "totalDue", "salesTrend", "last7Days", "dailySales", "dailyPurchase", "topProducts", "categoryStock", "recentSales", "lowStockProducts", "expiringProducts", "recentContactMessages", "recentSubscribers"));
    }
}
