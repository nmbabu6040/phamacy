<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Slider;
use App\Models\Faq;
use App\Models\Counter;
use App\Models\AboutFeature;
use App\Models\AboutValue;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $sliders = Slider::where("status", 1)->orderBy("sort_order")->get();
        $categories = Category::where("status", 1)->withCount("products")->orderBy("name")->limit(8)->get();
        $featuredProducts = Product::where("status", 1)->where("is_featured", 1)->with(["category", "generic"])->limit(8)->get();
        $newProducts = Product::where("status", 1)->with(["category", "generic"])->latest()->limit(8)->get();
        $counters = Counter::where("status", 1)->orderBy("sort_order")->get();
        $faqs = Faq::active()->orderBy('sort_order')->get();


        return view("frontend.home.index", compact("sliders", "categories", "featuredProducts", "newProducts", "counters", "faqs"));
    }

    public function about()
    {
        $aboutFeatures = AboutFeature::active()->orderBy('sort_order')->get();
        $aboutValues = AboutValue::active()->orderBy('sort_order')->get();
        return view("frontend.pages.about", compact("aboutFeatures", "aboutValues"));
    }
    public function contact()
    {
        return view("frontend.pages.contact");
    }

    public function contactSubmit(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'subject' => ['nullable', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        ContactMessage::create($validated);

        return back()->with(
            'success',
            'Thank you! Your message has been sent successfully. We will get back to you soon.'
        );
    }

    public function newsletterSubscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:150'],
        ]);

        $subscriber = NewsletterSubscriber::where(
            'email',
            $validated['email']
        )->first();

        if ($subscriber) {

            if (!$subscriber->status) {
                $subscriber->update([
                    'status' => true,
                    'subscribed_at' => now(),
                ]);

                return back()->with(
                    'newsletter_success',
                    'Welcome back! Your newsletter subscription has been reactivated.'
                );
            }

            return back()->with(
                'newsletter_success',
                'This email is already subscribed to our newsletter.'
            );
        }

        NewsletterSubscriber::create([
            'email' => $validated['email'],
            'status' => true,
            'subscribed_at' => now(),
        ]);

        return back()->with(
            'newsletter_success',
            'Thank you! You have successfully subscribed to our newsletter.'
        );
    }
}
