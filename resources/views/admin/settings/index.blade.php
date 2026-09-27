@extends('layouts.admin')
@section('title', 'Site Settings')
@section('content')
    <h4 class="mb-3">Site Settings &amp; SEO</h4>
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
            <div class="col-lg-7">
                <div class="card mb-3">
                    <div class="card-header fw-bold"><i class="bi bi-shop"></i> General</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Site Name</label><input name="site_name"
                                    class="form-control" value="{{ $settings['site_name'] ?? '' }}"></div>
                            <div class="col-md-6"><label class="form-label">Tagline</label><input name="site_tagline"
                                    class="form-control" value="{{ $settings['site_tagline'] ?? '' }}"></div>
                            <div class="col-md-6"><label class="form-label">Phone</label><input name="phone"
                                    class="form-control" value="{{ $settings['phone'] ?? '' }}"></div>
                            <div class="col-md-6"><label class="form-label">Email</label><input name="email"
                                    class="form-control" value="{{ $settings['email'] ?? '' }}"></div>
                            <div class="col-md-12"><label class="form-label">Address</label>
                                <textarea name="address" class="form-control">{{ $settings['address'] ?? '' }}</textarea>
                            </div>
                            <div class="col-md-6"><label class="form-label">Currency Symbol</label><input
                                    name="currency_symbol" class="form-control"
                                    value="{{ $settings['currency_symbol'] ?? '৳' }}"></div>
                            <div class="col-md-6">
                                <label class="form-label">Logo</label>
                                <input type="file" name="site_logo" class="form-control" accept="image/*">
                                @if (!empty($settings['site_logo']))
                                    <img src="{{ Storage::url($settings['site_logo']) }}" class="img-thumbnail mt-2"
                                        style="max-height: 100px;">
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Favicon</label>
                                <input type="file" name="site_favicon" class="form-control" accept="image/*">
                                @if (!empty($settings['site_favicon']))
                                    <img src="{{ Storage::url($settings['site_favicon']) }}" class="img-thumbnail mt-2"
                                        style="max-height: 100px;">
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">FAQ Section Image</label>
                                <input type="file" name="faq_image" class="form-control" accept="image/*">
                                @if (!empty($settings['faq_image']))
                                    <img src="{{ Storage::url($settings['faq_image']) }}" class="img-thumbnail mt-2"
                                        style="max-height: 100px;">
                                @endif
                            </div>
                            <div class="col-md-12"><label class="form-label">Footer Text</label><input name="footer_text"
                                    class="form-control" value="{{ $settings['footer_text'] ?? '' }}"></div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header fw-bold"><i class="bi bi-share"></i> Social Links</div>
                    <div class="card-body row g-3">
                        <div class="col-md-6"><label class="form-label">Facebook</label><input name="facebook"
                                class="form-control" value="{{ $settings['facebook'] ?? '' }}"></div>
                        <div class="col-md-6"><label class="form-label">Instagram</label><input name="instagram"
                                class="form-control" value="{{ $settings['instagram'] ?? '' }}"></div>
                        <div class="col-md-6"><label class="form-label">Twitter / X</label><input name="twitter"
                                class="form-control" value="{{ $settings['twitter'] ?? '' }}"></div>
                        <div class="col-md-6"><label class="form-label">YouTube</label><input name="youtube"
                                class="form-control" value="{{ $settings['youtube'] ?? '' }}"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header fw-bold"><i class="bi bi-search"></i> SEO Settings</div>
                    <div class="card-body">
                        <label class="form-label">Meta Title</label><input name="meta_title" class="form-control mb-2"
                            value="{{ $settings['meta_title'] ?? '' }}">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" class="form-control mb-2">{{ $settings['meta_description'] ?? '' }}</textarea>
                        <label class="form-label">Meta Keywords</label><input name="meta_keywords"
                            class="form-control mb-2" value="{{ $settings['meta_keywords'] ?? '' }}">
                        <label class="form-label">OG Image (social share)</label><input type="file" name="og_image"
                            class="form-control mb-2">
                        <label class="form-label">Google Analytics ID</label><input name="google_analytics_id"
                            class="form-control" value="{{ $settings['google_analytics_id'] ?? '' }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-lg-7">
                <div class="card p-4">
                    <h5 class="mt-4">About Page — Intro Section</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Intro Image</label>
                            <input type="file" name="about_image" class="form-control" accept="image/*">
                            @if (!empty($settings['about_image']))
                                <img src="{{ Storage::url($settings['about_image']) }}" class="img-thumbnail mt-2"
                                    style="max-height:120px">
                            @endif
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Badge Text</label>
                            <input type="text" name="about_badge" class="form-control"
                                placeholder="e.g. About YourPharmacy"
                                value="{{ old('about_badge', $settings['about_badge'] ?? '') }}">
                            <label class="form-label mt-2">Heading</label>
                            <input type="text" name="about_heading" class="form-control"
                                value="{{ old('about_heading', $settings['about_heading'] ?? '') }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea name="about_description" rows="3" class="form-control">{{ old('about_description', $settings['about_description'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card p-4">
                    <h5 class="mt-4">Contact Page — Map</h5>
                    <div class="col-md-12">
                        <label class="form-label">Google Maps Embed URL</label>
                        <input type="url" name="map_embed_url" class="form-control"
                            placeholder="https://www.google.com/maps?q=Dhaka,Bangladesh&output=embed"
                            value="{{ old('map_embed_url', $settings['map_embed_url'] ?? '') }}">
                        <small class="text-muted">
                            Google Maps-এ গিয়ে লোকেশন সার্চ করুন → Share → Embed a map → শুধু
                            <code>src="..."</code>-এর
                            ভেতরের
                            URL টুকু কপি করে এখানে বসান।
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <button class="btn btn-primary btn-lg mt-3"><i class="bi bi-check2"></i> Save Settings</button>
    </form>
@endsection
