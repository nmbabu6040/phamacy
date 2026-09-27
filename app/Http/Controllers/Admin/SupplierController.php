<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::when($request->search, fn($q) => $q->where("name", "like", "%{$request->search}%"))
            ->latest()->paginate(15)->withQueryString();

        return view("admin.suppliers.index", compact("suppliers"));
    }

    public function store(Request $request)
    {
        $data = $request->validate(["name" => "required|string|max:255", "company_name" => "nullable|string|max:255", "phone" => "nullable|string|max:30", "email" => "nullable|email", "address" => "nullable|string", "status" => "boolean"]);
        if (array_key_exists("name", $data) && \Illuminate\Support\Facades\Schema::hasColumn("suppliers", "slug")) {
            $data["slug"] = Str::slug($data["name"]) . "-" . Str::random(4);
        }

        $item = Supplier::create($data);
        ActivityLog::record("created", "Supplier", "Created suppliers: " . ($item->name ?? $item->id));

        return back()->with("success", "Supplier created successfully.");
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate(["name" => "required|string|max:255", "company_name" => "nullable|string|max:255", "phone" => "nullable|string|max:30", "email" => "nullable|email", "address" => "nullable|string", "status" => "boolean"]);
        $supplier->update($data);
        ActivityLog::record("updated", "Supplier", "Updated suppliers: " . ($supplier->name ?? $supplier->id));

        return back()->with("success", "Supplier updated successfully.");
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        ActivityLog::record("deleted", "Supplier", "Deleted a suppliers record");

        return back()->with("success", "Supplier deleted successfully.");
    }
}
