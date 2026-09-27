<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::when($request->search, fn($q) => $q->where("name", "like", "%{$request->search}%"))
            ->latest()->paginate(15)->withQueryString();

        return view("admin.customers.index", compact("customers"));
    }

    public function store(Request $request)
    {
        $data = $request->validate(["name" => "required|string|max:255", "phone" => "nullable|string|max:30", "email" => "nullable|email", "address" => "nullable|string", "status" => "boolean"]);
        if (array_key_exists("name", $data) && \Illuminate\Support\Facades\Schema::hasColumn("customers", "slug")) {
            $data["slug"] = Str::slug($data["name"]) . "-" . Str::random(4);
        }

        $item = Customer::create($data);
        ActivityLog::record("created", "Customer", "Created customers: " . ($item->name ?? $item->id));

        return back()->with("success", "Customer created successfully.");
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate(["name" => "required|string|max:255", "phone" => "nullable|string|max:30", "email" => "nullable|email", "address" => "nullable|string", "status" => "boolean"]);
        $customer->update($data);
        ActivityLog::record("updated", "Customer", "Updated customers: " . ($customer->name ?? $customer->id));

        return back()->with("success", "Customer updated successfully.");
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        ActivityLog::record("deleted", "Customer", "Deleted a customers record");

        return back()->with("success", "Customer deleted successfully.");
    }
}
