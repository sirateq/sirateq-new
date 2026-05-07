<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Signed links (email / SMS) — guest can open order / invoice without prior session.
    |--------------------------------------------------------------------------
    */
    'signed_url_expiry_days' => max(1, (int) env('SHOP_ORDER_SIGNED_URL_EXPIRY_DAYS', 60)),

    'admin_emails' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('SHOP_ADMIN_NOTIFICATION_EMAILS', 'info@sirateqghana.com'))
    ))),

    'admin_phone_numbers' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('SHOP_ADMIN_NOTIFICATION_PHONES', ''))
    ))),

];
