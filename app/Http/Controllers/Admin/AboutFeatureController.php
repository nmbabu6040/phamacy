<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutFeature;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AboutFeatureController extends Controller
{
    public function index(Request $request)
    {
        $items = AboutFeature::when($request->search, fn($q) => $q->where('text', 'like', "%{$request->search}%"))
            ->orderBy('sort_order')->paginate(15)->withQueryString();

        return view('admin.about-features.index', compact('items'));
    }

    public function create()
    {
        return view('admin.about-features.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'text' => 'required|string|max:255',
            'sort_order' => 'nullable|integer',
            'status' => 'boolean',
        ]);

        $item = AboutFeature::create($data);
        ActivityLog::record('created', 'AboutFeature', 'Created about feature: ' . $item->text);

        return redirect()->route('admin.about-features.index')->with('success', 'Feature created successfully.');
    }

    public function edit(AboutFeature $aboutFeature)
    {
        return view('admin.about-features.edit', ['item' => $aboutFeature]);
    }

    public function update(Request $request, AboutFeature $aboutFeature)
    {
        $data = $request->validate([
            'text' => 'required|string|max:255',
            'sort_order' => 'nullable|integer',
            'status' => 'boolean',
        ]);

        $aboutFeature->update($data);
        ActivityLog::record('updated', 'AboutFeature', 'Updated about feature: ' . $aboutFeature->text);

        return redirect()->route('admin.about-features.index')->with('success', 'Feature updated successfully.');
    }

    public function destroy(AboutFeature $aboutFeature)
    {
        $aboutFeature->delete();
        ActivityLog::record('deleted', 'AboutFeature', 'Deleted an about feature record');

        return back()->with('success', 'Feature deleted successfully.');
    }
}
