<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111;
        }

        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }

        .brand {
            font-size: 16px;
            font-weight: bold;
            color: #4f46e5;
        }

        h2 {
            font-size: 15px;
            margin: 0 0 4px;
        }

        .muted {
            color: #6b7280;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 5px 7px;
            text-align: left;
            font-size: 10px;
        }

        th {
            background: #f3f4f6;
        }

        .summary-table td {
            border: none;
            padding: 2px 8px;
            font-size: 11px;
        }

        .text-end {
            text-align: right;
        }

        .summary-box {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 10px;
            display: inline-block;
        }
    </style>
</head>

<body>
    <div class="header">
        <div>
            <div class="brand">{{ $siteSettings['site_name'] ?? config('app.name') }}</div>
            <div class="muted">Generated {{ now()->format('d M Y, h:i A') }}</div>
        </div>
        <div style="text-align:right">
            <h2>{{ $title }}</h2>
            <div class="muted">{{ ucfirst($range) }} range: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} &ndash;
                {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</div>
        </div>
    </div>

    @if (!empty($summary))
        <div class="summary-box">
            <table class="summary-table">
                @foreach ($summary as $label => $value)
                    <tr>
                        <td>{{ $label }}</td>
                        <td><b>{{ $value }}</b></td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endif

    <table>
        <thead>
            <tr>
                @foreach ($headers as $h)
                    <th>{{ $h }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    @foreach ($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) }}" style="text-align:center">No data in this range.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="muted" style="margin-top:16px">{{ count($rows) }} record(s). Computer-generated report from
        {{ config('app.name') }}.</p>
</body>

</html>
