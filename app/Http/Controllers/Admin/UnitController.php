<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $units = Unit::when($request->search, fn($q) => $q->where("name", "like", "%{$request->search}%"))
            ->latest()->paginate(15)->withQueryString();

        return view("admin.units.index", compact("units"));
    }

    public function store(Request $request)
    {
        $data = $request->validate(["name" => "required|string|max:100", "short_name" => "required|string|max:20"]);
        if (array_key_exists("name", $data) && \Illuminate\Support\Facades\Schema::hasColumn("units", "slug")) {
            $data["slug"] = Str::slug($data["name"]) . "-" . Str::random(4);
        }

        $item = Unit::create($data);
        ActivityLog::record("created", "Unit", "Created units: " . ($item->name ?? $item->id));

        return back()->with("success", "Unit created successfully.");
    }

    public function update(Request $request, Unit $unit)
    {
        $data = $request->validate(["name" => "required|string|max:100", "short_name" => "required|string|max:20"]);
        $unit->update($data);
        ActivityLog::record("updated", "Unit", "Updated units: " . ($unit->name ?? $unit->id));

        return back()->with("success", "Unit updated successfully.");
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();
        ActivityLog::record("deleted", "Unit", "Deleted a units record");

        return back()->with("success", "Unit deleted successfully.");
    }
}
