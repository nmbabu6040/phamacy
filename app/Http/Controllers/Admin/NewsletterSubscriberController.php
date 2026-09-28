<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterSubscriberController extends Controller
{
    public function index(Request $request)
    {
        $subscribers = NewsletterSubscriber::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(
                    'email',
                    'like',
                    '%' . $request->search . '%'
                );
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view(
            'admin.newsletter-subscribers.index',
            compact('subscribers')
        );
    }

    public function update(
        Request $request,
        NewsletterSubscriber $newsletterSubscriber
    ) {
        $validated = $request->validate([
            'status' => ['required', 'boolean'],
        ]);

        $newsletterSubscriber->update($validated);

        return back()->with(
            'success',
            'Subscriber status updated successfully.'
        );
    }

    public function destroy(
        NewsletterSubscriber $newsletterSubscriber
    ) {
        $newsletterSubscriber->delete();

        return redirect()
            ->route('admin.newsletter-subscribers.index')
            ->with(
                'success',
                'Subscriber deleted successfully.'
            );
    }
}
