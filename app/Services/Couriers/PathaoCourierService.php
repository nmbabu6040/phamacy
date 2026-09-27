<?php
namespace App\Services\Couriers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Pathao Courier (Merchant / Hermes API) — https://merchant.pathao.com
 * Docs: available from your Pathao merchant dashboard > Integration > API docs.
 * Auth: OAuth2 password grant -> Bearer access token (cached until it expires).
 *
 * IMPORTANT: Pathao requires every order to include a city_id / zone_id / area_id
 * that matches ITS OWN address database (not free-text address matching). This
 * driver uses the default_city_id/zone_id/area_id from config/couriers.php unless
 * the caller passes ["meta" => ["city_id"=>.., "zone_id"=>.., "area_id"=>..]] —
 * use getCities()/getZones()/getAreas() below to build a proper address picker
 * if you deliver to more than one zone.
 */
class PathaoCourierService extends AbstractCourierProvider
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config("couriers.pathao.base_url"), "/");
    }

    public function name(): string { return "pathao"; }

    /** Access tokens are short-lived; cache and reuse until near expiry. */
    protected function token(): ?string
    {
        return Cache::remember("pathao_access_token", now()->addMinutes(50), function () {
            $response = Http::asJson()->post("{$this->baseUrl}/aggregator/oauth/issue-token", [
                "client_id" => config("couriers.pathao.client_id"),
                "client_secret" => config("couriers.pathao.client_secret"),
                "username" => config("couriers.pathao.username"),
                "password" => config("couriers.pathao.password"),
                "grant_type" => "password",
            ]);

            return $response->json("access_token");
        });
    }

    protected function client()
    {
        return Http::withToken($this->token())->baseUrl($this->baseUrl);
    }

    public function createOrder(array $order): array
    {
        return $this->safeRequest(function () use ($order) {
            $meta = $order["meta"] ?? [];

            $response = $this->client()->post("/aggregator/orders", [
                "store_id" => config("couriers.pathao.store_id"),
                "merchant_order_id" => $order["invoice"],
                "recipient_name" => $order["recipient_name"],
                "recipient_phone" => $order["recipient_phone"],
                "recipient_address" => $order["recipient_address"],
                "recipient_city" => $meta["city_id"] ?? config("couriers.pathao.default_city_id"),
                "recipient_zone" => $meta["zone_id"] ?? config("couriers.pathao.default_zone_id"),
                "recipient_area" => $meta["area_id"] ?? config("couriers.pathao.default_area_id"),
                "delivery_type" => $meta["delivery_type"] ?? 48, // 48 = normal delivery
                "item_type" => 2, // 2 = parcel
                "special_instruction" => $order["note"] ?? null,
                "item_quantity" => $meta["item_quantity"] ?? 1,
                "item_weight" => $order["weight"] ?? 0.5,
                "item_description" => $order["item_description"] ?? "Medicine order",
                "amount_to_collect" => $order["cod_amount"],
            ]);

            $data = $response->json();
            $consignment = $data["data"] ?? null;

            return [
                "success" => $response->successful() && $consignment,
                "consignment_id" => $consignment["consignment_id"] ?? null,
                "tracking_code" => $consignment["consignment_id"] ?? null, // Pathao tracks by consignment id
                "message" => $data["message"] ?? ($response->successful() ? "Order created" : "Failed to create order"),
                "raw" => $data ?? [],
            ];
        }, "createOrder");
    }

    public function getStatus(string $consignmentId): array
    {
        return $this->safeRequest(function () use ($consignmentId) {
            $response = $this->client()->get("/aggregator/orders/{$consignmentId}/info");
            $data = $response->json();

            return [
                "success" => $response->successful(),
                "status" => $data["data"]["order_status"] ?? null,
                "raw" => $data ?? [],
            ];
        }, "getStatus");
    }

    /** Address lookup helpers — build a City -> Zone -> Area picker with these if needed. */
    public function getCities(): array
    {
        return $this->client()->get("/aggregator/cities")->json("data.data") ?? [];
    }

    public function getZones(int $cityId): array
    {
        return $this->client()->get("/aggregator/cities/{$cityId}/zone-list")->json("data.data") ?? [];
    }

    public function getAreas(int $zoneId): array
    {
        return $this->client()->get("/aggregator/zones/{$zoneId}/area-list")->json("data.data") ?? [];
    }
}
