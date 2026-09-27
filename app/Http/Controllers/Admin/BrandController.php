<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $brands = Brand::when($request->search, fn($q) => $q->where("name", "like", "%{$request->search}%"))
            ->latest()->paginate(15)->withQueryString();

        return view("admin.brands.index", compact("brands"));
    }

    public function store(Request $request)
    {
        $data = $request->validate(["name" => "required|string|max:255", "logo" => "nullable|image|max:2048"]);
        if (array_key_exists("name", $data) && \Illuminate\Support\Facades\Schema::hasColumn("brands", "slug")) {
            $data["slug"] = Str::slug($data["name"]) . "-" . Str::random(4);
        }

        $item = Brand::create($data);
        ActivityLog::record("created", "Brand", "Created brands: " . ($item->name ?? $item->id));

        return back()->with("success", "Brand created successfully.");
    }

    public function update(Request $request, Brand $brand)
    {
        $data = $request->validate(["name" => "required|string|max:255", "logo" => "nullable|image|max:2048"]);
        $brand->update($data);
        ActivityLog::record("updated", "Brand", "Updated brands: " . ($brand->name ?? $brand->id));

        return back()->with("success", "Brand updated successfully.");
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();
        ActivityLog::record("deleted", "Brand", "Deleted a brands record");

        return back()->with("success", "Brand deleted successfully.");
    }
}
