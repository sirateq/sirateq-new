<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Return policy (Merchant Center & customers)
    |--------------------------------------------------------------------------
    |
    | Google Merchant Center expects a URL on your store where customers can
    | read your return policy. Use the default on-site page at the
    | shop.policies.returns route, or set STOREFRONT_RETURN_POLICY_URL to a
    | full absolute URL if you host the canonical policy elsewhere (same brand).
    |
    */
    'return_policy_url' => env('STOREFRONT_RETURN_POLICY_URL'),

    /*
    | Optional extra link (e.g. longer PDF or help center) shown on the policy page.
    */
    'return_policy_details_url' => env('STOREFRONT_RETURN_POLICY_DETAILS_URL'),

    /*
    | Countries or regions that share your shipping costs and delivery options
    | (display only). Pipe-separated, e.g. "Ghana|Togo"
    */
    'shipping_countries' => array_values(array_filter(array_map(
        trim(...),
        explode('|', (string) env('STOREFRONT_SHIPPING_COUNTRIES', 'Ghana'))
    ))),

];
