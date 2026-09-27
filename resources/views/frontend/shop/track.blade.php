@extends("layouts.frontend")
@section("title", "Track Your Order")
@section("content")
<div class="page-banner"><div class="container"><h2 data-aos="fade-up">Track Your Order</h2></div></div>
<section class="py-5">
    <div class="container">
        @if(session("error"))<div class="alert alert-danger">{{ session("error") }}</div>@endif
        <div class="row justify-content-center">
            <div class="col-lg-6" data-aos="fade-up">
                <form action="{{ route('order.track.find') }}" method="POST" class="card p-4 mb-4">
                    @csrf
                    <label class="form-label">Invoice Number</label>
                    <input name="invoice_no" class="form-control mb-3" placeholder="ORD-XXXXXXXX" required>
                    <label class="form-label">Phone Number</label>
                    <input name="phone" class="form-control mb-3" required>
                    <button class="btn btn-primary">Track Order</button>
                </form>
            </div>
        </div>

        @isset($sale)
        <div class="row justify-content-center" data-aos="fade-up">
            <div class="col-lg-8">
                <div class="card p-4">
                    <div class="d-flex justify-content-between mb-3">
                        <h5>Order {{ $sale->invoice_no }}</h5>
                        <span class="badge bg-primary fs-6">{{ ucfirst($sale->order_status) }}</span>
                    </div>
                    <ul class="list-group list-group-flush mb-3">
                        @foreach($sale->items as $item)
                            <li class="list-group-item d-flex justify-content-between">
                                <span>{{ $item->product->name ?? "Product" }} × {{ $item->quantity }}</span>
                                <span>৳{{ number_format($item->subtotal,2) }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="d-flex justify-content-between fw-bold"><span>Total</span><span>৳{{ number_format($sale->grand_total,2) }}</span></div>
                </div>
            </div>
        </div>
        @endisset
    </div>
</section>
@endsection
