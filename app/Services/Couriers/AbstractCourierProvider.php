<?php
namespace App\Services\Couriers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class AbstractCourierProvider implements CourierProviderInterface
{
    /**
     * Default bulk implementation: loop over createOrder() one at a time.
     * Providers with a real native bulk endpoint (e.g. Steadfast) override this
     * for a single network call instead.
     */
    public function bulkCreateOrders(array $orders): array
    {
        $results = [];
        foreach ($orders as $order) {
            try {
                $results[] = $this->createOrder($order);
            } catch (\Throwable $e) {
                Log::error("[{$this->name()}] bulk order failed: " . $e->getMessage());
                $results[] = ["success" => false, "consignment_id" => null, "tracking_code" => null, "message" => $e->getMessage(), "raw" => []];
            }
        }
        return $results;
    }

    /** Shared helper so every provider logs/handles HTTP failures the same way. */
    protected function safeRequest(\Closure $callback, string $context): array
    {
        try {
            return $callback();
        } catch (\Throwable $e) {
            Log::error("[{$this->name()}] {$context}: " . $e->getMessage());
            return ["success" => false, "consignment_id" => null, "tracking_code" => null, "message" => "Courier API error: " . $e->getMessage(), "raw" => []];
        }
    }
}
