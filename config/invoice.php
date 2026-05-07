<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Company details on PDF invoices
    |--------------------------------------------------------------------------
    */

    'company' => [
        'name' => env('INVOICE_COMPANY_NAME', 'Sirateq Ghana Group Ltd'),
        'address' => env('INVOICE_ADDRESS', 'Alhaji Junction, Greater Accra, Ghana'),
        'email' => env('INVOICE_EMAIL', 'info@sirateqghana.com'),
        'phone' => env('INVOICE_PHONE', '+233 36 229 6798'),
    ],

    /** Relative to the public/ directory */
    'logo' => env('INVOICE_LOGO', 'logo.png'),

    /** Payment due date shown on the invoice (days after invoice / order date) */
    'payment_due_days' => (int) env('INVOICE_PAYMENT_DUE_DAYS', 14),

    /** Primary accent for headers, table band, and totals */
    'accent' => env('INVOICE_ACCENT', '#1565C0'),

];
