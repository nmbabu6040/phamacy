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
        $data = $request->validate([
            "name" => "required|string|max:255",
            "logo" => "nullable|image|mimes:jpeg,jpg,png,webp,gif,svg,bmp,ico,tiff,tga,jfif,avif|max:3072"
        ]);
        $data["slug"] = Str::slug($data["name"]) . "-" . Str::random(4);

        if ($request->hasFile("logo")) {
            $data["logo"] = $request->file("logo")->store("brands", "public");
        }

        $item = Brand::create($data);
        ActivityLog::record("created", "Brand", "Created brand: {$item->name}");

        return back()->with("success", "Brand created successfully.");
    }

    public function update(Request $request, Brand $brand)
    {
        $data = $request->validate([
            "name" => "required|string|max:255",
            "logo" => "nullable|image|mimes:jpeg,jpg,png,webp,gif,svg,bmp,ico,tiff,tga,jfif,avif|max:3072"
        ]);

        if ($request->hasFile("logo")) {
            $data["logo"] = $request->file("logo")->store("brands", "public");
        }

        $brand->update($data);
        ActivityLog::record("updated", "Brand", "Updated brand: {$brand->name}");

        return back()->with("success", "Brand updated successfully.");
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();
        ActivityLog::record("deleted", "Brand", "Deleted a brands record");

        return back()->with("success", "Brand deleted successfully.");
    }
}
