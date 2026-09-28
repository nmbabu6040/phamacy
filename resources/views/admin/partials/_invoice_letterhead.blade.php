{{--
    Reusable invoice letterhead -- shop identity block shown at the top of every
    printed invoice (sale or purchase). Included inside .print-area so it shows
    on screen-print too, not just the PDF download.

    Expects (all optional, falls back gracefully):
      $branch -> App\Models\Branch|null   the branch this transaction happened at
--}}
<div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
    <div class="d-flex align-items-center gap-3">
        @if (!empty($siteSettings['site_logo']))
            <img src="{{ asset('storage/' . $siteSettings['site_logo']) }}" alt="Logo"
                style="height:56px;max-width:120px;object-fit:contain">
        @endif
        <div>
            <h5 class="mb-0 fw-bold">{{ $siteSettings['site_name'] ?? config('app.name') }}</h5>
            @if (!empty($siteSettings['site_tagline']))
                <div class="small text-muted">{{ $siteSettings['site_tagline'] }}</div>
            @endif
            <div class="small text-muted">
                @if ($branch ?? null)
                    {{ $branch->name }}{{ $branch->address ? ' -- ' . $branch->address : '' }}
                @elseif(!empty($siteSettings['address']))
                    {{ $siteSettings['address'] }}
                @endif
            </div>
            <div class="small text-muted">
                @php($phone = $branch->phone ?? null ?: $siteSettings['phone'] ?? null)
                @php($email = $branch->email ?? null ?: $siteSettings['email'] ?? null)
                @if ($phone)
                    <i class="bi bi-telephone"></i> {{ $phone }}
                @endif
                @if ($phone && $email)
                    &nbsp;|&nbsp;
                @endif
                @if ($email)
                    <i class="bi bi-envelope"></i> {{ $email }}
                @endif
            </div>
        </div>
    </div>
</div>
