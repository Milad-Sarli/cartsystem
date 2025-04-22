<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cart System Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure various settings for the cart system including
    | route prefixes, middleware, expiration times, and item limits.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | API Route Prefix
    |--------------------------------------------------------------------------
    |
    | This value is the prefix that will be used for all cart-related API routes.
    | You may modify this prefix to avoid conflicts with your existing routes.
    |
    */
    'route_prefix' => env('CART_ROUTE_PREFIX', 'api/v1'),

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware will be assigned to all cart-related routes, giving you
    | the chance to add your own middleware to this array or change any of
    | the existing middleware.
    |
    */
    'middleware' => [
        'api',
        'auth:sanctum'
    ],

    /*
    |--------------------------------------------------------------------------
    | Item Expiration
    |--------------------------------------------------------------------------
    |
    | This value determines how long (in hours) cart items will remain valid
    | before being automatically removed. Set to null to disable expiration.
    |
    */
    'item_expiration' => env('CART_ITEM_EXPIRATION', 24),

    /*
    |--------------------------------------------------------------------------
    | Maximum Items
    |--------------------------------------------------------------------------
    |
    | This value determines the maximum number of items that can be stored in
    | a single cart. This helps prevent abuse and server overload.
    |
    */
    'max_items' => env('CART_MAX_ITEMS', 50),

    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | This value determines which database connection to use for cart storage.
    | By default, it will use your default database connection.
    |
    */
    'database_connection' => env('CART_DB_CONNECTION', null),

    /*
    |--------------------------------------------------------------------------
    | Cart Events
    |--------------------------------------------------------------------------
    |
    | Enable or disable cart events for actions like adding, updating,
    | or removing items. This allows for custom event listeners.
    |
    */
    'events_enabled' => env('CART_EVENTS_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Price Precision
    |--------------------------------------------------------------------------
    |
    | Define the number of decimal places to use for price calculations.
    | This ensures consistent price handling across the application.
    |
    */
    'price_precision' => env('CART_PRICE_PRECISION', 2),

    /*
    |--------------------------------------------------------------------------
    | Tax Calculation
    |--------------------------------------------------------------------------
    |
    | Enable or disable automatic tax calculations for cart items.
    | When enabled, you can set tax rates per product or globally.
    |
    */
    'tax_calculation_enabled' => env('CART_TAX_ENABLED', true),
    'default_tax_rate' => env('CART_DEFAULT_TAX_RATE', 0),

    // Default currency for prices
    'currency' => 'IRR',

    // Payment gateway configuration
    'payment' => [
        'driver' => 'zarinpal',
        'callback_url' => '/payment/callback',
    ],

    // Product model configuration
    'models' => [
        'product' => \App\Models\Product::class,
        'user' => \App\Models\User::class,
        'address' => \App\Models\Address::class,
        'tenant' => \App\Models\Tenant::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Multi-tenancy Support
    |--------------------------------------------------------------------------
    |
    | Enable or disable multi-tenancy support for the cart system.
    | When disabled, tenant_id will be ignored in all operations.
    |
    */
    'multi_tenancy_enabled' => env('CART_MULTI_TENANCY_ENABLED', false),
];
