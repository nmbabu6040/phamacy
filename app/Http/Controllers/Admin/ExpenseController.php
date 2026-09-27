<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $expenses = Expense::with("category")
            ->when($request->from, fn ($q) => $q->whereDate("expense_date", ">=", $request->from))
            ->when($request->to, fn ($q) => $q->whereDate("expense_date", "<=", $request->to))
            ->when($request->category_id, fn ($q) => $q->where("expense_category_id", $request->category_id))
            ->latest()->paginate(15)->withQueryString();

        $categories = ExpenseCategory::orderBy("name")->get();
        $totalExpense = $expenses->sum("amount");

        return view("admin.expenses.index", compact("expenses", "categories", "totalExpense"));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            "expense_category_id" => "required|exists:expense_categories,id",
            "title" => "required|string|max:255",
            "amount" => "required|numeric|min:0",
            "expense_date" => "required|date",
            "attachment" => "nullable|file|max:4096",
            "note" => "nullable|string",
        ]);

        if ($request->hasFile("attachment")) {
            $data["attachment"] = $request->file("attachment")->store("expenses", "public");
        }
        $data["created_by"] = auth()->id();

        $expense = Expense::create($data);
        ActivityLog::record("created", "Expense", "Recorded expense: {$expense->title} (৳{$expense->amount})", $expense);

        return back()->with("success", "Expense recorded successfully.");
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        ActivityLog::record("deleted", "Expense", "Deleted expense: {$expense->title}");

        return back()->with("success", "Expense deleted successfully.");
    }
}
