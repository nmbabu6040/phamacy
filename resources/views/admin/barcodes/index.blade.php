@extends("layouts.admin")
@section("title", "Barcode / QR Labels")
@section("content")
<h4 class="mb-3">Print Barcode &amp; QR Labels</h4>
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.barcodes.print') }}" method="GET" target="_blank">
            <input type="text" id="productFilter" class="form-control mb-3" placeholder="Search product...">
            <div class="table-responsive" style="max-height:500px; overflow-y:auto">
                <table class="table table-hover align-middle">
                    <thead class="table-light sticky-top"><tr><th style="width:40px"></th><th>Product</th><th>Code</th><th>Price</th><th style="width:120px">Labels</th></tr></thead>
                    <tbody id="productRows">
                    @foreach($products as $p)
                        <tr class="product-row" data-name="{{ strtolower($p->name) }}">
                            <td><input type="checkbox" name="product_ids[]" value="{{ $p->id }}" class="form-check-input product-check"></td>
                            <td>{{ $p->name }}</td>
                            <td>{{ $p->code }}</td>
                            <td>৳{{ number_format($p->sale_price,2) }}</td>
                            <td><input type="number" name="quantities[{{ $p->id }}]" class="form-control form-control-sm" value="1" min="1"></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <button class="btn btn-primary mt-3"><i class="bi bi-printer"></i> Generate Labels</button>
        </form>
    </div>
</div>
@endsection
@push("scripts")
<script>
$("#productFilter").on("input", function () {
    const q = $(this).val().toLowerCase();
    $(".product-row").each(function () {
        $(this).toggle($(this).data("name").includes(q));
    });
});
</script>
@endpush
