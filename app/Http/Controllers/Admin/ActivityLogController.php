<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = ActivityLog::with("user")
            ->when($request->module, fn ($q) => $q->where("module", $request->module))
            ->when($request->user_id, fn ($q) => $q->where("user_id", $request->user_id))
            ->when($request->from, fn ($q) => $q->whereDate("created_at", ">=", $request->from))
            ->when($request->to, fn ($q) => $q->whereDate("created_at", "<=", $request->to))
            ->latest()->paginate(30)->withQueryString();

        $modules = ActivityLog::select("module")->distinct()->pluck("module");

        return view("admin.activity-logs.index", compact("logs", "modules"));
    }

    public function destroy(ActivityLog $activityLog)
    {
        $activityLog->delete();
        return back()->with("success", "Log entry removed.");
    }

    public function clear()
    {
        ActivityLog::truncate();
        return back()->with("success", "All activity logs cleared.");
    }
}
