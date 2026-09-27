<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Counter;
use Illuminate\Http\Request;

class CounterController extends Controller
{
    public function index(Request $request)
    {
        $counters = Counter::when($request->search, fn($q) => $q->where('label', 'like', "%{$request->search}%"))
            ->orderBy('sort_order')
            ->paginate(15)
            ->withQueryString();

        return view('admin.counters.index', compact('counters'));
    }

    public function create()
    {
        return view('admin.counters.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'icon' => 'required|string|max:100',
            'count' => 'required|integer|min:0',
            'label' => 'required|string|max:255',
            'sort_order' => 'nullable|integer',
            'status' => 'boolean',
        ]);

        $item = Counter::create($data);
        ActivityLog::record('created', 'Counter', 'Created counter: ' . $item->label);

        return redirect()->route('admin.counters.index')->with('success', 'Counter created successfully.');
    }

    public function edit(Counter $counter)
    {
        return view('admin.counters.edit', compact('counter'));
    }

    public function update(Request $request, Counter $counter)
    {
        $data = $request->validate([
            'icon' => 'required|string|max:100',
            'count' => 'required|integer|min:0',
            'label' => 'required|string|max:255',
            'sort_order' => 'nullable|integer',
            'status' => 'boolean',
        ]);

        $counter->update($data);
        ActivityLog::record('updated', 'Counter', 'Updated counter: ' . $counter->label);

        return redirect()->route('admin.counters.index')->with('success', 'Counter updated successfully.');
    }

    public function destroy(Counter $counter)
    {
        $counter->delete();
        ActivityLog::record('deleted', 'Counter', 'Deleted a counter record');

        return back()->with('success', 'Counter deleted successfully.');
    }
}
