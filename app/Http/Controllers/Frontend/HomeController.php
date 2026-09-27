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

    public function contactSubmit()
    {
        // Contact form handling placeholder — wire up mail/notification here later.
        return back()->with("success", "Thank you! Your message has been sent. We will get back to you soon.");
    }
}
