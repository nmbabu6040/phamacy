<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Print Labels</title>
<style>
    body { font-family: Arial, sans-serif; margin: 10px; }
    .label-grid { display: flex; flex-wrap: wrap; gap: 8px; }
    .label { width: 190px; border: 1px dashed #999; border-radius: 6px; padding: 8px; text-align: center; page-break-inside: avoid; }
    .label .name { font-size: 12px; font-weight: bold; margin-bottom: 2px; height: 30px; overflow: hidden; }
    .label .price { font-size: 13px; color: #111; margin: 4px 0; }
    .codes-row { display: flex; align-items: center; justify-content: center; gap: 6px; }
    .codes-row svg { max-height: 45px; }
    @media print { .no-print { display: none; } }
</style>
</head>
<body>
    <div class="no-print mb-3"><button onclick="window.print()">🖨️ Print</button></div>
    <div class="label-grid">
        @foreach($labels as $p)
            <div class="label">
                <div class="name">{{ $p->name }}</div>
                <div class="price">৳{{ number_format($p->sale_price,2) }}</div>
                <div class="codes-row">
                    {!! \App\Services\BarcodeService::svg($p->code, 1, 30) !!}
                </div>
                <div style="margin-top:4px">{!! QrCode::size(60)->generate(url('/shop/'.$p->slug)) !!}</div>
                <div style="font-size:9px;color:#666">{{ $p->code }}</div>
            </div>
        @endforeach
    </div>
</body>
</html>
