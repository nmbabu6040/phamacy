@extends("layouts.admin")
@section("title", "Backup")
@section("content")
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Database &amp; File Backup</h4>
    <form action="{{ route('admin.backups.run') }}" method="POST">@csrf
        <button class="btn btn-primary"><i class="bi bi-cloud-arrow-up"></i> Run Backup Now</button>
    </form>
</div>
<div class="alert alert-info"><i class="bi bi-info-circle"></i> Backups are created via <code>spatie/laravel-backup</code> (<code>php artisan backup:run</code>) and stored under <code>storage/app/backup</code>. A daily automatic backup is scheduled at 2:00 AM (see <code>routes/console.php</code>) — make sure the server cron is configured to run <code>php artisan schedule:run</code> every minute.</div>
<div class="card"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>File</th><th>Size</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody>
        @forelse($files as $f)
            <tr>
                <td><i class="bi bi-file-zip"></i> {{ $f["name"] }}</td>
                <td>{{ $f["size"] }}</td>
                <td>{{ \Carbon\Carbon::createFromTimestamp($f["date"])->format("d M Y, h:i A") }}</td>
                <td>
                    <a href="{{ route('admin.backups.download', $f['name']) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i></a>
                    <form action="{{ route('admin.backups.destroy', $f['name']) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this backup?')">@csrf @method("DELETE")<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="text-center text-muted py-4">No backups yet. Click "Run Backup Now" to create one.</td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>
@endsection
