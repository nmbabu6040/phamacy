@extends("layouts.frontend")
@section("title", "Order Placed")
@section("content")
<section class="py-5">
    <div class="container text-center">
        <i class="bi bi-check-circle-fill text-success display-1" data-aos="zoom-in"></i>
        <h2 class="mt-3" data-aos="fade-up">Thank you, {{ $sale->customer_name }}!</h2>
        <p class="text-muted" data-aos="fade-up">Your order has been placed successfully.</p>

        <div class="card mx-auto mt-4 p-4 text-start" style="max-width:500px" data-aos="fade-up">
            <p><b>Invoice No:</b> {{ $sale->invoice_no }}</p>
            <p><b>Status:</b> <span class="badge bg-secondary">{{ ucfirst($sale->order_status) }}</span></p>
            <p><b>Total:</b> ৳{{ number_format($sale->grand_total,2) }}</p>
            <p><b>Payment:</b> {{ ucfirst(str_replace("_"," ",$sale->payment_method)) }}</p>
            <hr>
            <p class="mb-0 small text-muted">A confirmation has been sent to your email/SMS. Save your invoice number and phone number to track your order.</p>
        </div>

        <div class="mt-4" data-aos="fade-up">
            <a href="{{ route('order.track') }}" class="btn btn-outline-primary me-2">Track This Order</a>
            <a href="{{ route('shop.index') }}" class="btn btn-primary">Continue Shopping</a>
        </div>
    </div>
</section>
@endsection
