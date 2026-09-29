<?php

namespace App\Http\Controllers\Admin;

use App\Exports\GenericArrayExport;
use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /** Shared sales report: daily / weekly / monthly / yearly, filterable by custom date range. */
    public function sales(Request $request)
    {
        $range = $request->get("range", "monthly"); // daily|weekly|monthly|yearly|custom
        [$from, $to] = $this->resolveRange($request, $range);

        $sales = Sale::whereBetween("sale_date", [$from, $to])->where("status", "completed")->with("customer");
        $summary = [
            "count" => (clone $sales)->count(),
            "total" => (clone $sales)->sum("grand_total"),
            "profit" => (clone $sales)->sum("profit"),
            "due" => (clone $sales)->sum("due_amount"),
        ];

        if ($export = $this->maybeExport(
            $request,
            "Sales Report",
            fn() => (clone $sales)->latest()->get(),
            ["Invoice", "Customer", "Date", "Total", "Profit", "Due"],
            fn($s) => [$s->invoice_no, $s->customer->name ?? $s->customer_name ?? "Walk-in", $s->sale_date->format("d M Y"), number_format($s->grand_total, 2), number_format($s->profit, 2), number_format($s->due_amount, 2)],
            $range,
            $from,
            $to,
            ["Total Invoices" => $summary["count"], "Total Sales" => "৳" . number_format($summary["total"], 2), "Total Profit" => "৳" . number_format($summary["profit"], 2)]
        )) return $export;

        $chart = Sale::select(DB::raw("DATE(sale_date) as d"), DB::raw("SUM(grand_total) as total"))
            ->whereBetween("sale_date", [$from, $to])->groupBy("d")->orderBy("d")->get();

        $list = $sales->latest()->paginate(20)->withQueryString();

        return view("admin.reports.sales", compact("list", "summary", "chart", "range", "from", "to"));
    }

    public function purchases(Request $request)
    {
        $range = $request->get("range", "monthly");
        [$from, $to] = $this->resolveRange($request, $range);

        $purchases = Purchase::whereBetween("purchase_date", [$from, $to])->where("status", "!=", "cancelled")->with("supplier");
        $summary = [
            "count" => (clone $purchases)->count(),
            "total" => (clone $purchases)->sum("grand_total"),
            "due" => (clone $purchases)->sum("due_amount"),
        ];

        if ($export = $this->maybeExport(
            $request,
            "Purchase Report",
            fn() => (clone $purchases)->latest()->get(),
            ["Invoice", "Supplier", "Date", "Total", "Due"],
            fn($p) => [$p->invoice_no, $p->supplier->name ?? "-", $p->purchase_date->format("d M Y"), number_format($p->grand_total, 2), number_format($p->due_amount, 2)],
            $range,
            $from,
            $to,
            ["Total Purchases" => $summary["count"], "Total Amount" => "৳" . number_format($summary["total"], 2), "Total Due" => "৳" . number_format($summary["due"], 2)]
        )) return $export;

        $list = $purchases->latest()->paginate(20)->withQueryString();

        return view("admin.reports.purchases", compact("list", "summary", "range", "from", "to"));
    }

    /** Sales that were cancelled/returned (Sale::status = cancelled) — stock was already restored at cancel time. */
    public function salesReturns(Request $request)
    {
        $range = $request->get("range", "monthly");
        [$from, $to] = $this->resolveRange($request, $range);

        $returns = Sale::where("status", "cancelled")->whereBetween("sale_date", [$from, $to])->with(["customer", "branch"]);
        $summary = [
            "count" => (clone $returns)->count(),
            "total" => (clone $returns)->sum("grand_total"),
        ];

        if ($export = $this->maybeExport(
            $request,
            "Sales Return Report",
            fn() => (clone $returns)->latest()->get(),
            ["Invoice", "Customer", "Branch", "Date", "Amount"],
            fn($s) => [$s->invoice_no, $s->customer->name ?? $s->customer_name ?? "Walk-in", $s->branch->name ?? "-", $s->sale_date->format("d M Y"), number_format($s->grand_total, 2)],
            $range,
            $from,
            $to,
            ["Total Returns" => $summary["count"], "Total Value" => "৳" . number_format($summary["total"], 2)]
        )) return $export;

        $list = $returns->latest()->paginate(20)->withQueryString();

        return view("admin.reports.sales-returns", compact("list", "summary", "range", "from", "to"));
    }

    /** Purchases that were returned to the supplier (Purchase::status = cancelled). */
    public function purchaseReturns(Request $request)
    {
        $range = $request->get("range", "monthly");
        [$from, $to] = $this->resolveRange($request, $range);

        $returns = Purchase::where("status", "cancelled")->whereBetween("purchase_date", [$from, $to])->with(["supplier", "branch"]);
        $summary = [
            "count" => (clone $returns)->count(),
            "total" => (clone $returns)->sum("grand_total"),
        ];

        if ($export = $this->maybeExport(
            $request,
            "Purchase Return Report",
            fn() => (clone $returns)->latest()->get(),
            ["Invoice", "Supplier", "Branch", "Date", "Amount"],
            fn($p) => [$p->invoice_no, $p->supplier->name ?? "-", $p->branch->name ?? "-", $p->purchase_date->format("d M Y"), number_format($p->grand_total, 2)],
            $range,
            $from,
            $to,
            ["Total Returns" => $summary["count"], "Total Value" => "৳" . number_format($summary["total"], 2)]
        )) return $export;

        $list = $returns->latest()->paginate(20)->withQueryString();

        return view("admin.reports.purchase-returns", compact("list", "summary", "range", "from", "to"));
    }

    public function expenses(Request $request)
    {
        $range = $request->get("range", "monthly");
        [$from, $to] = $this->resolveRange($request, $range);

        $expenses = Expense::whereBetween("expense_date", [$from, $to])->with("category");
        $byCategory = (clone $expenses)->select("expense_category_id", DB::raw("SUM(amount) as total"))
            ->groupBy("expense_category_id")->with("category:id,name")->get();
        $total = (clone $expenses)->sum("amount");

        if ($export = $this->maybeExport(
            $request,
            "Expense Report",
            fn() => (clone $expenses)->latest()->get(),
            ["Title", "Category", "Amount", "Date", "Note"],
            fn($e) => [$e->title, $e->category->name ?? "-", number_format($e->amount, 2), $e->expense_date->format("d M Y"), $e->note ?? ""],
            $range,
            $from,
            $to,
            ["Total Expenses" => "৳" . number_format($total, 2)]
        )) return $export;

        $list = $expenses->latest()->paginate(20)->withQueryString();

        return view("admin.reports.expenses", compact("list", "total", "byCategory", "range", "from", "to"));
    }

    /** Profit & Loss statement: Sales revenue - COGS - Operating Expenses = Net Profit. */
    public function profitLoss(Request $request)
    {
        $range = $request->get("range", "monthly");
        [$from, $to] = $this->resolveRange($request, $range);

        $revenue = Sale::whereBetween("sale_date", [$from, $to])->where("status", "completed")->sum("grand_total");
        $grossProfit = Sale::whereBetween("sale_date", [$from, $to])->where("status", "completed")->sum("profit");
        $cogs = $revenue - $grossProfit;
        $expenseTotal = Expense::whereBetween("expense_date", [$from, $to])->sum("amount");
        $netProfit = $grossProfit - $expenseTotal;

        if ($request->filled("export")) {
            $headers = ["Line Item", "Amount"];
            $rows = [
                ["Total Revenue (Sales)", number_format($revenue, 2)],
                ["Cost of Goods Sold (COGS)", "-" . number_format($cogs, 2)],
                ["Gross Profit", number_format($grossProfit, 2)],
                ["Operating Expenses", "-" . number_format($expenseTotal, 2)],
                ["Net Profit / Loss", number_format($netProfit, 2)],
            ];
            $export = $this->exportRows($request, "Profit and Loss Statement", $headers, $rows, $range, $from, $to);
            if ($export) return $export;
        }

        $monthly = Sale::select(
            DB::raw("DATE_FORMAT(sale_date, \"%Y-%m\") as ym"),
            DB::raw("SUM(grand_total) as revenue"),
            DB::raw("SUM(profit) as gross_profit")
        )->whereBetween("sale_date", [$from, $to])->where("status", "completed")->groupBy("ym")->orderBy("ym")->get();

        return view("admin.reports.profit-loss", compact(
            "revenue",
            "grossProfit",
            "cogs",
            "expenseTotal",
            "netProfit",
            "monthly",
            "range",
            "from",
            "to"
        ));
    }

    public function inventory(Request $request)
    {
        $products = Product::with(["category", "generic"]);
        $totalStockValue = Product::sum(DB::raw("stock_qty * purchase_price"));
        $lowStockCount = Product::whereColumn("stock_qty", "<=", "alert_qty")->count();

        if ($export = $this->maybeExport(
            $request,
            "Inventory Report",
            fn() => (clone $products)->orderBy("stock_qty")->get(),
            ["Product", "Category", "Generic", "Stock Qty", "Purchase Price", "Stock Value"],
            fn($p) => [$p->name, $p->category->name ?? "-", $p->generic->name ?? "-", $p->stock_qty, number_format($p->purchase_price, 2), number_format($p->stock_qty * $p->purchase_price, 2)],
            "current",
            now(),
            now(),
            ["Total Stock Value" => "৳" . number_format($totalStockValue, 2), "Low Stock Items" => $lowStockCount]
        )) return $export;

        $products = $products->orderBy("stock_qty")->paginate(25);

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

    /**
     * If the request asks for an export (?export=pdf or ?export=excel), builds the full
     * (unpaginated) dataset via $fetch + $mapRow and returns a download response.
     * Returns null when no export was requested, so the caller just renders the view as normal.
     */
    private function maybeExport(Request $request, string $title, \Closure $fetch, array $headers, \Closure $mapRow, string $range, $from, $to, array $summary = [])
    {
        if (!$request->filled("export")) return null;

        $rows = $fetch()->map($mapRow)->toArray();

        return $this->exportRows($request, $title, $headers, $rows, $range, $from, $to, $summary);
    }

    private function exportRows(Request $request, string $title, array $headers, array $rows, string $range, $from, $to, array $summary = [])
    {
        $filename = Str::slug($title) . "-" . now()->format("Ymd_His");

        if ($request->export === "excel") {
            return Excel::download(new GenericArrayExport($headers, $rows, $title), "{$filename}.xlsx");
        }

        return Pdf::loadView("admin.reports.pdf", compact("title", "headers", "rows", "range", "from", "to", "summary"))
            ->setPaper("a4", "landscape")->download("{$filename}.pdf");
    }
}
