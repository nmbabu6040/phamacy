<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categorys = Category::when($request->search, fn($q) => $q->where("name", "like", "%{$request->search}%"))
            ->latest()->paginate(15)->withQueryString();

        return view("admin.categories.index", compact("categorys"));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            "name" => "required|string|max:255",
            "image" => "nullable|image|mimes:jpeg,jpg,png,webp,gif,svg,bmp,ico,tiff,tga,jfif,avif|max:3072",
            "status" => "boolean"
        ]);

        $data["slug"] = Str::slug($data["name"]) . "-" . Str::random(4);

        if ($request->hasFile("image")) {
            $data["image"] = $request->file("image")->store("categories", "public");
        }

        $item = Category::create($data);
        ActivityLog::record("created", "Category", "Created category: {$item->name}");

        return back()->with("success", "Category created successfully.");
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            "name" => "required|string|max:255",
            "image" => "nullable|image|mimes:jpeg,jpg,png,webp,gif,svg,bmp,ico,tiff,tga,jfif,avif|max:3072",
            "status" => "boolean"
        ]);

        if ($request->hasFile("image")) {
            $data["image"] = $request->file("image")->store("categories", "public");
        }

        $category->update($data);
        ActivityLog::record("updated", "Category", "Updated category: {$category->name}");

        return back()->with("success", "Category updated successfully.");
    }

    public function destroy(Category $category)
    {
        $category->delete();
        ActivityLog::record("deleted", "Category", "Deleted a categories record");

        return back()->with("success", "Category deleted successfully.");
    }
}
