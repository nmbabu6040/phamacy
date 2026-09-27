<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /** Shared sales report: daily / weekly / monthly / yearly, filterable by custom date range. */
    public function sales(Request $request)
    {
        $range = $request->get("range", "monthly"); // daily|weekly|monthly|yearly|custom
        [$from, $to] = $this->resolveRange($request, $range);

        $sales = Sale::whereBetween("sale_date", [$from, $to])->with("customer");
        $summary = [
            "count" => (clone $sales)->count(),
            "total" => (clone $sales)->sum("grand_total"),
            "profit" => (clone $sales)->sum("profit"),
            "due" => (clone $sales)->sum("due_amount"),
        ];

        $chart = Sale::select(DB::raw("DATE(sale_date) as d"), DB::raw("SUM(grand_total) as total"))
            ->whereBetween("sale_date", [$from, $to])->groupBy("d")->orderBy("d")->get();

        $list = $sales->latest()->paginate(20)->withQueryString();

        return view("admin.reports.sales", compact("list", "summary", "chart", "range", "from", "to"));
    }

    public function purchases(Request $request)
    {
        $range = $request->get("range", "monthly");
        [$from, $to] = $this->resolveRange($request, $range);

        $purchases = Purchase::whereBetween("purchase_date", [$from, $to])->with("supplier");
        $summary = [
            "count" => (clone $purchases)->count(),
            "total" => (clone $purchases)->sum("grand_total"),
            "due" => (clone $purchases)->sum("due_amount"),
        ];

        $list = $purchases->latest()->paginate(20)->withQueryString();

        return view("admin.reports.purchases", compact("list", "summary", "range", "from", "to"));
    }

    public function expenses(Request $request)
    {
        $range = $request->get("range", "monthly");
        [$from, $to] = $this->resolveRange($request, $range);

        $expenses = Expense::whereBetween("expense_date", [$from, $to])->with("category");
        $byCategory = (clone $expenses)->select("expense_category_id", DB::raw("SUM(amount) as total"))
            ->groupBy("expense_category_id")->with("category:id,name")->get();

        $list = $expenses->latest()->paginate(20)->withQueryString();
        $total = (clone $expenses)->sum("amount");

        return view("admin.reports.expenses", compact("list", "total", "byCategory", "range", "from", "to"));
    }

    /** Profit & Loss statement: Sales revenue - COGS - Operating Expenses = Net Profit. */
    public function profitLoss(Request $request)
    {
        $range = $request->get("range", "monthly");
        [$from, $to] = $this->resolveRange($request, $range);

        $revenue = Sale::whereBetween("sale_date", [$from, $to])->sum("grand_total");
        $grossProfit = Sale::whereBetween("sale_date", [$from, $to])->sum("profit");
        $cogs = $revenue - $grossProfit;
        $expenseTotal = Expense::whereBetween("expense_date", [$from, $to])->sum("amount");
        $netProfit = $grossProfit - $expenseTotal;

        $monthly = Sale::select(
                DB::raw("DATE_FORMAT(sale_date, \"%Y-%m\") as ym"),
                DB::raw("SUM(grand_total) as revenue"),
                DB::raw("SUM(profit) as gross_profit")
            )->whereBetween("sale_date", [$from, $to])->groupBy("ym")->orderBy("ym")->get();

        return view("admin.reports.profit-loss", compact(
            "revenue","grossProfit","cogs","expenseTotal","netProfit","monthly","range","from","to"
        ));
    }

    public function inventory()
    {
        $products = Product::with(["category","generic"])->orderBy("stock_qty")->paginate(25);
        $totalStockValue = Product::sum(DB::raw("stock_qty * purchase_price"));
        $lowStockCount = Product::whereColumn("stock_qty", "<=", "alert_qty")->count();

        return view("admin.reports.inventory", compact("products", "totalStockValue", "lowStockCount"));
    }

    private function resolveRange(Request $request, string $range): array
    {
        if ($range === "custom" && $request->from && $request->to) {
            return [Carbon::parse($request->from)->startOfDay(), Carbon::parse($request->to)->endOfDay()];
        }

        return match ($range) {
            "daily" => [Carbon::today(), Carbon::today()->endOfDay()],
            "weekly" => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            "yearly" => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            default => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()], // monthly
        };
    }
}
