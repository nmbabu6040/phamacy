<?php
namespace App\Services\Couriers;

use Illuminate\Support\Facades\Http;

/**
 * Steadfast Courier Ltd — https://steadfast.com.bd
 * Docs (merchant panel > API docs): https://portal.steadfast.com.bd/documentation
 * Auth: "Api-Key" + "Secret-Key" headers (found in Settings > API in the merchant panel).
 */
class SteadfastCourierService extends AbstractCourierProvider
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $secretKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config("couriers.steadfast.base_url"), "/");
        $this->apiKey = config("couriers.steadfast.api_key");
        $this->secretKey = config("couriers.steadfast.secret_key");
    }

    public function name(): string { return "steadfast"; }

    protected function client()
    {
        return Http::withHeaders([
            "Api-Key" => $this->apiKey,
            "Secret-Key" => $this->secretKey,
            "Content-Type" => "application/json",
        ])->baseUrl($this->baseUrl);
    }

    public function createOrder(array $order): array
    {
        return $this->safeRequest(function () use ($order) {
            $response = $this->client()->post("/create_order", [
                "invoice" => $order["invoice"],
                "recipient_name" => $order["recipient_name"],
                "recipient_phone" => $order["recipient_phone"],
                "recipient_address" => $order["recipient_address"],
                "cod_amount" => $order["cod_amount"],
                "note" => $order["note"] ?? $order["item_description"] ?? null,
            ]);

            $data = $response->json();
            $consignment = $data["consignment"] ?? null;

            return [
                "success" => $response->successful() && $consignment,
                "consignment_id" => $consignment["consignment_id"] ?? null,
                "tracking_code" => $consignment["tracking_code"] ?? null,
                "message" => $data["message"] ?? ($response->successful() ? "Order created" : "Failed to create order"),
                "raw" => $data ?? [],
            ];
        }, "createOrder");
    }

    /** Steadfast has a real bulk endpoint: the whole batch goes as one JSON-encoded "data" field. */
    public function bulkCreateOrders(array $orders): array
    {
        try {
            $payload = array_map(fn ($o) => [
                "invoice" => $o["invoice"],
                "recipient_name" => $o["recipient_name"],
                "recipient_phone" => $o["recipient_phone"],
                "recipient_address" => $o["recipient_address"],
                "cod_amount" => $o["cod_amount"],
                "note" => $o["note"] ?? $o["item_description"] ?? null,
            ], $orders);

            $response = $this->client()->post("/create_order/bulk-order", [
                "data" => json_encode($payload),
            ]);

            $rows = $response->json("data") ?? [];
            if (empty($rows)) {
                // Endpoint responded but with no per-row breakdown — fall back to individual calls.
                return parent::bulkCreateOrders($orders);
            }

            // Steadfast returns one result object per submitted invoice, same order as sent.
            return array_map(function ($row) {
                $consignment = $row["consignment"] ?? null;
                return [
                    "success" => ($row["status"] ?? null) == 200 && $consignment,
                    "consignment_id" => $consignment["consignment_id"] ?? null,
                    "tracking_code" => $consignment["tracking_code"] ?? null,
                    "message" => $row["message"] ?? "Processed",
                    "raw" => $row,
                ];
            }, $rows);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("[steadfast] bulkCreateOrders: " . $e->getMessage());
            // Return one failure result per input order so the caller can still show a per-row report.
            return array_fill(0, count($orders), [
                "success" => false, "consignment_id" => null, "tracking_code" => null,
                "message" => "Courier API error: " . $e->getMessage(), "raw" => [],
            ]);
        }
    }

    public function getStatus(string $consignmentId): array
    {
        return $this->safeRequest(function () use ($consignmentId) {
            $response = $this->client()->get("/status_by_cid/{$consignmentId}");
            $data = $response->json();

            return [
                "success" => $response->successful(),
                "status" => $data["delivery_status"] ?? null,
                "raw" => $data ?? [],
            ];
        }, "getStatus");
    }

    /** Bonus helper (not part of the interface): check remaining courier account balance. */
    public function getBalance(): ?float
    {
        $response = $this->client()->get("/get_balance");
        return $response->json("current_balance");
    }
}
