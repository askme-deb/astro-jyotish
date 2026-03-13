<?php

return [
    'business' => [
        'name' => env('INVOICE_BUSINESS_NAME', 'Astrologer Raju Maharaj'),
        'address' => env('INVOICE_BUSINESS_ADDRESS', 'Bagda, P.S.-Puncha, Dist. - Purulia, West Bengal, Pin - 723151'),
        'gstin' => env('INVOICE_BUSINESS_GSTIN', '07ABCDE1234F1Z5'),
        'email' => env('INVOICE_BUSINESS_EMAIL', 'contact@astrorajumaharaj.com'),
        'phone' => env('INVOICE_BUSINESS_PHONE', '+91 90918 40899'),
    ],
    'invoice_prefix' => env('INVOICE_PREFIX', 'AJ'),
];
