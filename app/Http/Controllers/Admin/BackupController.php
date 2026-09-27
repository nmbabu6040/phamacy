<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    /**
     * Lists backup zip files created by spatie/laravel-backup
     * (php artisan backup:run) under storage/app/backup.
     */
    public function index()
    {
        $disk = Storage::disk("local");
        $files = collect($disk->allFiles("backup"))
            ->filter(fn ($f) => str_ends_with($f, ".zip"))
            ->map(fn ($f) => [
                "name" => basename($f),
                "path" => $f,
                "size" => round($disk->size($f) / 1048576, 2) . " MB",
                "date" => $disk->lastModified($f),
            ])->sortByDesc("date")->values();

        return view("admin.backups.index", compact("files"));
    }

    public function run()
    {
        Artisan::call("backup:run", ["--only-db" => false]);
        ActivityLog::record("created", "Backup", auth()->user()->name . " triggered a manual backup.");

        return back()->with("success", "Backup completed successfully.");
    }

    public function download(string $file)
    {
        $path = "backup/" . $file;
        abort_unless(Storage::disk("local")->exists($path), 404);

        return Storage::disk("local")->download($path);
    }

    public function destroy(string $file)
    {
        Storage::disk("local")->delete("backup/" . $file);
        ActivityLog::record("deleted", "Backup", "Deleted backup file: {$file}");

        return back()->with("success", "Backup file deleted.");
    }
}
