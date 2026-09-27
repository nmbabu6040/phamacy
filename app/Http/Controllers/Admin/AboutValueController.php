<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutValue;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AboutValueController extends Controller
{
    public function index(Request $request)
    {
        $items = AboutValue::when($request->search, fn($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->orderBy('sort_order')->paginate(15)->withQueryString();

        return view('admin.about-values.index', compact('items'));
    }

    public function create()
    {
        return view('admin.about-values.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'icon' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'sort_order' => 'nullable|integer',
            'status' => 'boolean',
        ]);

        $item = AboutValue::create($data);
        ActivityLog::record('created', 'AboutValue', 'Created about value: ' . $item->title);

        return redirect()->route('admin.about-values.index')->with('success', 'Value card created successfully.');
    }

    public function edit(AboutValue $aboutValue)
    {
        return view('admin.about-values.edit', ['item' => $aboutValue]);
    }

    public function update(Request $request, AboutValue $aboutValue)
    {
        $data = $request->validate([
            'icon' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'sort_order' => 'nullable|integer',
            'status' => 'boolean',
        ]);

        $aboutValue->update($data);
        ActivityLog::record('updated', 'AboutValue', 'Updated about value: ' . $aboutValue->title);

        return redirect()->route('admin.about-values.index')->with('success', 'Value card updated successfully.');
    }

    public function destroy(AboutValue $aboutValue)
    {
        $aboutValue->delete();
        ActivityLog::record('deleted', 'AboutValue', 'Deleted an about value record');

        return back()->with('success', 'Value card deleted successfully.');
    }
}
