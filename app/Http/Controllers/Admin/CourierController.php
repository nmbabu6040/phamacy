<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CourierBooking;
use App\Models\Sale;
use App\Services\Couriers\CourierManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CourierController extends Controller
{
    /** Bookings list + a queue of online orders that still need a courier booked. */
    public function index(Request $request)
    {
        $bookings = CourierBooking::with(["sale", "bookedBy"])
            ->when($request->provider, fn ($q) => $q->where("provider", $request->provider))
            ->when($request->status, fn ($q) => $q->where("status", $request->status))
            ->when($request->search, fn ($q) => $q->where("invoice_reference", "like", "%{$request->search}%")
                ->orWhere("recipient_phone", "like", "%{$request->search}%")
                ->orWhere("consignment_id", "like", "%{$request->search}%"))
            ->latest()->paginate(20)->withQueryString();

        // Online orders (paid/COD) that do not have a courier booking yet
        $pendingOrders = Sale::where("channel", "online")
            ->whereNotIn("order_status", ["delivered", "cancelled"])
            ->whereDoesntHave("courierBooking")
            ->latest()->limit(20)->get();

        return view("admin.courier.index", [
            "bookings" => $bookings,
            "pendingOrders" => $pendingOrders,
            "providers" => CourierManager::available(),
        ]);
    }

    /** Book a single existing online order with the chosen courier. */
    public function bookOrder(Request $request, Sale $sale)
    {
        $request->validate([
            "provider" => "required|string|in:" . implode(",", array_keys(CourierManager::available())),
            "weight" => "nullable|numeric|min:0",
            "note" => "nullable|string|max:255",
        ]);

        $booking = $this->createBooking($request->provider, [
            "invoice" => $sale->invoice_no,
            "recipient_name" => $sale->customer_name ?: ($sale->customer->name ?? "Customer"),
            "recipient_phone" => $sale->customer_phone ?: ($sale->customer->phone ?? ""),
            "recipient_address" => $sale->shipping_address ?: ($sale->customer->address ?? ""),
            "cod_amount" => $sale->payment_status === "paid" ? 0 : $sale->due_amount,
            "item_description" => "Pharmacy order {$sale->invoice_no}",
            "weight" => $request->weight ?? 0.5,
            "note" => $request->note,
        ], $sale);

        if ($booking->status === "failed") {
            return back()->with("error", "Courier booking failed: " . $booking->failure_reason);
        }

        $sale->update(["order_status" => "processing"]);
        ActivityLog::record("created", "CourierBooking", "Booked {$sale->invoice_no} with {$booking->providerLabel()}", $booking);

        return back()->with("success", "Order booked with {$booking->providerLabel()}. Consignment ID: " . ($booking->consignment_id ?? "pending"));
    }

    /** Ad-hoc single booking not tied to an existing Sale (e.g. a phone order). */
    public function bookAdhoc(Request $request)
    {
        $data = $request->validate([
            "provider" => "required|string|in:" . implode(",", array_keys(CourierManager::available())),
            "invoice_reference" => "required|string|max:100",
            "recipient_name" => "required|string|max:255",
            "recipient_phone" => "required|string|max:30",
            "recipient_address" => "required|string",
            "cod_amount" => "required|numeric|min:0",
            "item_description" => "nullable|string|max:255",
            "weight" => "nullable|numeric|min:0",
            "note" => "nullable|string|max:255",
        ]);

        $booking = $this->createBooking($data["provider"], [
            "invoice" => $data["invoice_reference"],
            "recipient_name" => $data["recipient_name"],
            "recipient_phone" => $data["recipient_phone"],
            "recipient_address" => $data["recipient_address"],
            "cod_amount" => $data["cod_amount"],
            "item_description" => $data["item_description"] ?? null,
            "weight" => $data["weight"] ?? 0.5,
            "note" => $data["note"] ?? null,
        ]);

        if ($booking->status === "failed") {
            return back()->with("error", "Courier booking failed: " . $booking->failure_reason);
        }

        return redirect()->route("admin.courier.index")->with("success", "Booked with {$booking->providerLabel()}.");
    }

    public function bulkForm()
    {
        return view("admin.courier.bulk", ["providers" => CourierManager::available()]);
    }

    public function downloadTemplate()
    {
        return response()->download(public_path("templates/courier-bulk-template.csv"));
    }

    /**
     * Bulk upload: a CSV or XLSX with columns
     * invoice_reference, recipient_name, recipient_phone, recipient_address, cod_amount, weight, item_description, note
     */
    public function bulkUpload(Request $request)
    {
        $request->validate([
            "provider" => "required|string|in:" . implode(",", array_keys(CourierManager::available())),
            "file" => "required|file|mimes:csv,txt,xlsx,xls|max:5120",
        ]);

        $rows = Excel::toArray([], $request->file("file"))[0] ?? [];
        if (empty($rows)) {
            return back()->with("error", "The uploaded file appears to be empty.");
        }

        $header = array_map(fn ($h) => strtolower(trim((string) $h)), array_shift($rows));
        $required = ["invoice_reference", "recipient_name", "recipient_phone", "recipient_address", "cod_amount"];
        foreach ($required as $col) {
            if (!in_array($col, $header)) {
                return back()->with("error", "Missing required column: {$col}. Please use the provided template.");
            }
        }

        $orders = [];
        $rowErrors = [];
        foreach ($rows as $i => $row) {
            if (count(array_filter($row, fn ($v) => $v !== null && $v !== "")) === 0) continue; // skip blank rows
            $assoc = array_combine($header, array_pad($row, count($header), null));

            if (empty($assoc["recipient_name"]) || empty($assoc["recipient_phone"]) || empty($assoc["recipient_address"])) {
                $rowErrors[] = "Row " . ($i + 2) . ": missing recipient name/phone/address — skipped.";
                continue;
            }

            $orders[] = [
                "invoice" => $assoc["invoice_reference"] ?: ("BULK-" . strtoupper(\Illuminate\Support\Str::random(8))),
                "recipient_name" => $assoc["recipient_name"],
                "recipient_phone" => $assoc["recipient_phone"],
                "recipient_address" => $assoc["recipient_address"],
                "cod_amount" => (float) ($assoc["cod_amount"] ?? 0),
                "weight" => isset($assoc["weight"]) && $assoc["weight"] !== "" ? (float) $assoc["weight"] : 0.5,
                "item_description" => $assoc["item_description"] ?? null,
                "note" => $assoc["note"] ?? null,
            ];
        }

        if (empty($orders)) {
            return back()->with("error", "No valid rows found in the file. " . implode(" ", $rowErrors));
        }

        $provider = CourierManager::driver($request->provider);
        $results = $provider->bulkCreateOrders($orders);

        $success = 0; $failed = 0;
        foreach ($orders as $i => $order) {
            $result = $results[$i] ?? ["success" => false, "message" => "No response from courier for this row"];

            CourierBooking::create([
                "provider" => $request->provider,
                "invoice_reference" => $order["invoice"],
                "consignment_id" => $result["consignment_id"] ?? null,
                "tracking_code" => $result["tracking_code"] ?? null,
                "recipient_name" => $order["recipient_name"],
                "recipient_phone" => $order["recipient_phone"],
                "recipient_address" => $order["recipient_address"],
                "cod_amount" => $order["cod_amount"],
                "item_description" => $order["item_description"],
                "weight" => $order["weight"],
                "status" => $result["success"] ? "booked" : "failed",
                "failure_reason" => $result["success"] ? null : ($result["message"] ?? "Unknown error"),
                "response_payload" => $result["raw"] ?? [],
                "booked_by" => auth()->id(),
            ]);

            $result["success"] ? $success++ : $failed++;
        }

        ActivityLog::record("created", "CourierBooking", "Bulk-booked {$success} order(s) via {$request->provider} ({$failed} failed)");

        $message = "{$success} order(s) booked successfully.";
        if ($failed) $message .= " {$failed} failed — check the bookings list for details.";
        if ($rowErrors) $message .= " " . count($rowErrors) . " row(s) skipped: " . implode(" ", array_slice($rowErrors, 0, 5));

        return redirect()->route("admin.courier.index")->with($failed ? "error" : "success", $message);
    }

    /** Pull the latest delivery status for one booking from the courier API. */
    public function refreshStatus(CourierBooking $booking)
    {
        if (!$booking->consignment_id) {
            return back()->with("error", "This booking has no consignment ID yet.");
        }

        $result = CourierManager::driver($booking->provider)->getStatus($booking->consignment_id);
        if ($result["success"] ?? false) {
            $booking->update([
                "status" => $this->mapStatus($result["status"] ?? null, $booking->status),
                "response_payload" => $result["raw"] ?? $booking->response_payload,
            ]);
            return back()->with("success", "Status updated: " . ($result["status"] ?? "unknown"));
        }

        return back()->with("error", "Could not fetch status from the courier right now.");
    }

    private function createBooking(string $providerName, array $orderData, ?Sale $sale = null): CourierBooking
    {
        $result = CourierManager::driver($providerName)->createOrder($orderData);

        return CourierBooking::create([
            "sale_id" => $sale?->id,
            "branch_id" => $sale?->branch_id,
            "provider" => $providerName,
            "invoice_reference" => $orderData["invoice"],
            "consignment_id" => $result["consignment_id"] ?? null,
            "tracking_code" => $result["tracking_code"] ?? null,
            "recipient_name" => $orderData["recipient_name"],
            "recipient_phone" => $orderData["recipient_phone"],
            "recipient_address" => $orderData["recipient_address"],
            "cod_amount" => $orderData["cod_amount"],
            "item_description" => $orderData["item_description"] ?? null,
            "weight" => $orderData["weight"] ?? null,
            "status" => ($result["success"] ?? false) ? "booked" : "failed",
            "failure_reason" => ($result["success"] ?? false) ? null : ($result["message"] ?? "Unknown error"),
            "response_payload" => $result["raw"] ?? [],
            "booked_by" => auth()->id(),
        ]);
    }

    /** Normalize each courier free-text status into our fixed enum, defaulting to the previous value. */
    private function mapStatus(?string $raw, string $fallback): string
    {
        $raw = strtolower((string) $raw);
        return match (true) {
            str_contains($raw, "deliver") => "delivered",
            str_contains($raw, "cancel") => "cancelled",
            str_contains($raw, "transit") || str_contains($raw, "hub") || str_contains($raw, "pickup") => "in_transit",
            default => $fallback,
        };
    }
}
