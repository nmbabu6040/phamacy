<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Generic;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GenericController extends Controller
{
    public function index(Request $request)
    {
        $generics = Generic::when($request->search, fn($q) => $q->where("name", "like", "%{$request->search}%"))
            ->latest()->paginate(15)->withQueryString();

        return view("admin.generics.index", compact("generics"));
    }

    public function store(Request $request)
    {
        $data = $request->validate(["name" => "required|string|max:255|unique:generics,name", "description" => "nullable|string"]);
        if (array_key_exists("name", $data) && \Illuminate\Support\Facades\Schema::hasColumn("generics", "slug")) {
            $data["slug"] = Str::slug($data["name"]) . "-" . Str::random(4);
        }

        $item = Generic::create($data);
        ActivityLog::record("created", "Generic", "Created generics: " . ($item->name ?? $item->id));

        return back()->with("success", "Generic created successfully.");
    }

    public function update(Request $request, Generic $generic)
    {
        $data = $request->validate(["name" => "required|string|max:255|unique:generics,name", "description" => "nullable|string"]);
        $generic->update($data);
        ActivityLog::record("updated", "Generic", "Updated generics: " . ($generic->name ?? $generic->id));

        return back()->with("success", "Generic updated successfully.");
    }

    public function destroy(Generic $generic)
    {
        $generic->delete();
        ActivityLog::record("deleted", "Generic", "Deleted a generics record");

        return back()->with("success", "Generic deleted successfully.");
    }
}
