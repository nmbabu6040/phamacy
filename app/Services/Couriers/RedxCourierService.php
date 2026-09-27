<?php
namespace App\Services\Couriers;

use Illuminate\Support\Facades\Http;

/**
 * RedX — https://redx.com.bd  |  Merchant API docs from your RedX merchant dashboard.
 * Auth: static "API-ACCESS-TOKEN" header (from merchant panel > Integration).
 *
 * Like Pathao, RedX needs a delivery_area_id matched against ITS OWN area list
 * (see getAreas() below) rather than a free-text address. Falls back to
 * config("couriers.redx.default_delivery_area_id") unless overridden per order
 * via $order["meta"]["delivery_area_id"].
 */
class RedxCourierService extends AbstractCourierProvider
{
    protected string $baseUrl;
    protected string $apiToken;

    public function __construct()
    {
        $this->baseUrl = rtrim(config("couriers.redx.base_url"), "/");
        $this->apiToken = config("couriers.redx.api_token");
    }

    public function name(): string { return "redx"; }

    protected function client()
    {
        return Http::withHeaders([
            "API-ACCESS-TOKEN" => "Bearer {$this->apiToken}",
            "Content-Type" => "application/json",
        ])->baseUrl($this->baseUrl);
    }

    public function createOrder(array $order): array
    {
        return $this->safeRequest(function () use ($order) {
            $meta = $order["meta"] ?? [];

            $response = $this->client()->post("/parcel", [
                "customer_name" => $order["recipient_name"],
                "customer_phone" => $order["recipient_phone"],
                "customer_address" => $order["recipient_address"],
                "delivery_area" => $meta["delivery_area_name"] ?? null,
                "delivery_area_id" => $meta["delivery_area_id"] ?? config("couriers.redx.default_delivery_area_id"),
                "pickup_store_id" => config("couriers.redx.pickup_store_id"),
                "merchant_invoice_id" => $order["invoice"],
                "cash_collection_amount" => $order["cod_amount"],
                "parcel_weight" => $order["weight"] ?? 0.5,
                "instruction" => $order["note"] ?? null,
                "value" => $order["cod_amount"],
                "parcel_details_json" => [[
                    "name" => $order["item_description"] ?? "Medicine order",
                    "category" => "Medicine",
                    "value" => $order["cod_amount"],
                ]],
            ]);

            $data = $response->json();

            return [
                "success" => $response->successful() && !empty($data["tracking_id"]),
                "consignment_id" => $data["tracking_id"] ?? null,
                "tracking_code" => $data["tracking_id"] ?? null,
                "message" => $data["message"] ?? ($response->successful() ? "Order created" : "Failed to create order"),
                "raw" => $data ?? [],
            ];
        }, "createOrder");
    }

    public function getStatus(string $consignmentId): array
    {
        return $this->safeRequest(function () use ($consignmentId) {
            $response = $this->client()->get("/parcel/track/{$consignmentId}");
            $data = $response->json();

            return [
                "success" => $response->successful(),
                "status" => $data["tracking"][0]["message"] ?? $data["status"] ?? null,
                "raw" => $data ?? [],
            ];
        }, "getStatus");
    }

    /** Area lookup helper — RedX orders need a delivery_area_id from this list. */
    public function getAreas(): array
    {
        return $this->client()->get("/areas")->json("areas") ?? [];
    }
}
