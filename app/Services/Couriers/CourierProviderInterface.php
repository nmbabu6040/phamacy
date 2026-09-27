<?php
namespace App\Services\Couriers;

interface CourierProviderInterface
{
    /**
     * Book a single order with the courier.
     *
     * @param array $order [
     *   "invoice"           => string   our own reference (e.g. sale invoice_no),
     *   "recipient_name"    => string,
     *   "recipient_phone"   => string,
     *   "recipient_address" => string,
     *   "cod_amount"        => float,
     *   "item_description"  => string|null,
     *   "weight"            => float|null (kg),
     *   "note"              => string|null,
     *   "meta"              => array|null  provider-specific extra fields (city_id, zone_id, area_id, etc.)
     * ]
     * @return array ["success"=>bool, "consignment_id"=>?string, "tracking_code"=>?string, "message"=>string, "raw"=>array]
     */
    public function createOrder(array $order): array;

    /**
     * Book many orders at once. Return one result array (same shape as createOrder())
     * per input order, in the same order they were given.
     *
     * @param array $orders array of order arrays, same shape as createOrder()
     * @return array[]
     */
    public function bulkCreateOrders(array $orders): array;

    /** Look up current delivery status from the courier by their consignment id. */
    public function getStatus(string $consignmentId): array;

    /** Machine name used in config/couriers.php and stored on courier_bookings.provider */
    public function name(): string;
}
