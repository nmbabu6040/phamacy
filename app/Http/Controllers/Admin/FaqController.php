<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $faqs = Faq::when($request->search, fn($q) => $q->where('question', 'like', "%{$request->search}%"))
            ->orderBy('sort_order')
            ->paginate(15)
            ->withQueryString();

        return view('admin.faqs.index', compact('faqs'));
    }

    public function create()
    {
        return view('admin.faqs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'sort_order' => 'nullable|integer',
            'status' => 'boolean',
        ]);

        $item = Faq::create($data);
        ActivityLog::record('created', 'Faq', 'Created FAQ: ' . $item->question);

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ created successfully.');
    }

    public function edit(Faq $faq)
    {
        return view('admin.faqs.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
        $data = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'sort_order' => 'nullable|integer',
            'status' => 'boolean',
        ]);

        $faq->update($data);
        ActivityLog::record('updated', 'Faq', 'Updated FAQ: ' . $faq->question);

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ updated successfully.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        ActivityLog::record('deleted', 'Faq', 'Deleted a FAQ record');

        return back()->with('success', 'FAQ deleted successfully.');
    }
}
