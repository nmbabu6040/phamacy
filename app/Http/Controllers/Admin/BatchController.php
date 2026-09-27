<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\ProductBatch;
use App\Notifications\ExpiryAlert;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class BatchController extends Controller
{
    /** Full batch ledger — every batch ever received, with remaining quantity & expiry. */
    public function index(Request $request)
    {
        $batches = ProductBatch::with(["product","branch"])
            ->when($request->search, fn ($q) => $q->where("batch_no", "like", "%{$request->search}%")
                ->orWhereHas("product", fn ($p) => $p->where("name", "like", "%{$request->search}%")))
            ->when($request->branch_id, fn ($q) => $q->where("branch_id", $request->branch_id))
            ->when($request->status === "active", fn ($q) => $q->where("quantity", ">", 0))
            ->when($request->status === "empty", fn ($q) => $q->where("quantity", "<=", 0))
            ->latest()->paginate(25)->withQueryString();

        $branches = Branch::orderBy("name")->get();

        return view("admin.batches.index", compact("batches", "branches"));
    }

    /** Expiring-soon / already-expired batches, with a button to email the alert to admins/managers. */
    public function expiring(Request $request)
    {
        $days = (int) $request->get("days", 90);

        $expiringSoon = ProductBatch::with(["product","branch"])
            ->where("quantity", ">", 0)
            ->whereNotNull("expiry_date")
            ->whereBetween("expiry_date", [now(), now()->addDays($days)])
            ->orderBy("expiry_date")->get();

        $alreadyExpired = ProductBatch::with(["product","branch"])
            ->where("quantity", ">", 0)
            ->whereNotNull("expiry_date")
            ->where("expiry_date", "<", now())
            ->orderBy("expiry_date")->get();

        if ($request->boolean("notify") && $expiringSoon->count()) {
            $admins = User::whereHas("role", fn ($q) => $q->whereIn("slug", ["admin","manager"]))->get();
            Notification::send($admins, new ExpiryAlert($expiringSoon, $days));
        }

        return view("admin.batches.expiring", compact("expiringSoon", "alreadyExpired", "days"));
    }
}
