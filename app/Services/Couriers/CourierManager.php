<?php
namespace App\Services\Couriers;

class CourierManager
{
    /** Resolve a courier driver instance by name (e.g. "steadfast"), or the configured default. */
    public static function driver(?string $name = null): CourierProviderInterface
    {
        $name = $name ?: config("couriers.default");
        $class = config("couriers.drivers.{$name}");

        if (!$class || !class_exists($class)) {
            throw new \InvalidArgumentException("Unknown or unconfigured courier provider: {$name}");
        }

        return app($class);
    }

    /** ["steadfast" => "Steadfast Courier", "pathao" => "Pathao Courier", ...] for dropdowns. */
    public static function available(): array
    {
        return config("couriers.providers", []);
    }
}
