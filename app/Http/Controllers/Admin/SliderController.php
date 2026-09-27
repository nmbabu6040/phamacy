<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
// use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\Validator;

class SliderController extends Controller
{
    public function index(Request $request)
    {
        $sliders = Slider::when($request->search, fn($q) => $q->where("title", "like", "%{$request->search}%"))
            ->orderBy("sort_order")->paginate(15)->withQueryString();

        return view("admin.sliders.index", compact("sliders"));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            "title" => "required|string|max:255",
            "subtitle" => "nullable|string",
            "button_text" => "nullable|string",
            "button_link" => "nullable|string",
            "image" => "required|image|mimes:jpeg,jpg,png,webp,gif,svg,bmp,ico,tiff,tga,jfif,avif|max:2048",
            "sort_order" => "nullable|integer",
            "status" => "boolean"
        ]);

        if ($request->hasFile("image")) {
            $data["image"] = $request->file("image")->store("sliders", "public");
        }

        $item = Slider::create($data);
        ActivityLog::record("created", "Slider", "Created slide: {$item->title}");

        return back()->with("success", "Slider created successfully.");
    }

    public function update(Request $request, Slider $slider)
    {
        $data = $request->validate([
            "title" => "required|string|max:255",
            "subtitle" => "nullable|string",
            "button_text" => "nullable|string",
            "button_link" => "nullable|string",
            "image" => "nullable|image|mimes:jpeg,jpg,png,webp,gif,svg,bmp,ico,tiff,tga,jfif,avif|max:2048",
            "sort_order" => "nullable|integer",
            "status" => "boolean"
        ]);

        if ($request->hasFile("image")) {
            $data["image"] = $request->file("image")->store("sliders", "public");
        }

        $slider->update($data);
        ActivityLog::record("updated", "Slider", "Updated slide: {$slider->title}");

        return back()->with("success", "Slider updated successfully.");
    }

    public function destroy(Slider $slider)
    {
        $slider->delete();
        ActivityLog::record("deleted", "Slider", "Deleted a slide");

        return back()->with("success", "Slider deleted successfully.");
    }
}
