<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request)
    {
        $expense_categorys = ExpenseCategory::when($request->search, fn($q) => $q->where("name", "like", "%{$request->search}%"))
            ->latest()->paginate(15)->withQueryString();

        return view("admin.expense-categories.index", compact("expense_categorys"));
    }

    public function store(Request $request)
    {
        $data = $request->validate(["name" => "required|string|max:255"]);
        if (array_key_exists("name", $data) && \Illuminate\Support\Facades\Schema::hasColumn("expense_categories", "slug")) {
            $data["slug"] = Str::slug($data["name"]) . "-" . Str::random(4);
        }

        $item = ExpenseCategory::create($data);
        ActivityLog::record("created", "ExpenseCategory", "Created expense_categories: " . ($item->name ?? $item->id));

        return back()->with("success", "ExpenseCategory created successfully.");
    }

    public function update(Request $request, ExpenseCategory $expense_category)
    {
        $data = $request->validate(["name" => "required|string|max:255"]);
        $expense_category->update($data);
        ActivityLog::record("updated", "ExpenseCategory", "Updated expense_categories: " . ($expense_category->name ?? $expense_category->id));

        return back()->with("success", "ExpenseCategory updated successfully.");
    }

    public function destroy(ExpenseCategory $expense_category)
    {
        $expense_category->delete();
        ActivityLog::record("deleted", "ExpenseCategory", "Deleted a expense_categories record");

        return back()->with("success", "ExpenseCategory deleted successfully.");
    }
}
