<?php

/**
 * Courier provider registry. Add a new courier by:
 *   1. Creating App\Services\Couriers\{Name}CourierService implementing CourierProviderInterface
 *   2. Registering it in the "drivers" map below
 *   3. Adding its credentials here (sourced from .env) and to .env.example
 *
 * NOTE: exact endpoint paths / field names for each courier below reflect their
 * publicly documented merchant APIs at the time this was written. Courier APIs
 * do change occasionally (new auth flow, renamed fields, etc.) -- if a provider
 * starts rejecting requests, check its latest merchant panel / API docs and
 * adjust the matching Service class; the rest of the system does not need to change.
 */
return [

    "default" => env("COURIER_DEFAULT", "steadfast"),

    // Human-readable labels shown in the provider dropdown throughout the admin UI.
    "providers" => [
        "steadfast" => "Steadfast Courier",
        "pathao" => "Pathao Courier",
        "redx" => "RedX",
    ],

    // Driver class map -- used by CourierManager::driver($name)
    "drivers" => [
        "steadfast" => \App\Services\Couriers\SteadfastCourierService::class,
        "pathao" => \App\Services\Couriers\PathaoCourierService::class,
        "redx" => \App\Services\Couriers\RedxCourierService::class,
    ],

    "steadfast" => [
        "base_url" => env("STEADFAST_BASE_URL", "https://portal.steadfast.com.bd/api/v1"),
        "api_key" => env("STEADFAST_API_KEY"),
        "secret_key" => env("STEADFAST_SECRET_KEY"),
    ],

    "pathao" => [
        "base_url" => env("PATHAO_BASE_URL", "https://api-hermes.pathao.com"),
        "client_id" => env("PATHAO_CLIENT_ID"),
        "client_secret" => env("PATHAO_CLIENT_SECRET"),
        "username" => env("PATHAO_USERNAME"),
        "password" => env("PATHAO_PASSWORD"),
        "store_id" => env("PATHAO_STORE_ID"),
        "default_city_id" => env("PATHAO_DEFAULT_CITY_ID"),
        "default_zone_id" => env("PATHAO_DEFAULT_ZONE_ID"),
        "default_area_id" => env("PATHAO_DEFAULT_AREA_ID"),
    ],

    "redx" => [
        "base_url" => env("REDX_BASE_URL", "https://openapi.redx.com.bd/v1.0.0-beta"),
        "api_token" => env("REDX_API_TOKEN"),
        "pickup_store_id" => env("REDX_PICKUP_STORE_ID"),
        "default_delivery_area_id" => env("REDX_DEFAULT_AREA_ID"),
    ],
];
