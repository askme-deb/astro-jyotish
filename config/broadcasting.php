<?php

return [

    'default' => env('BROADCAST_CONNECTION', 'null'),

    'connections' => [

        'pusher' => [
            'driver' => 'pusher',
            'key' => env('REVERB_APP_KEY', env('PUSHER_APP_KEY')),
            'secret' => env('REVERB_APP_SECRET', env('PUSHER_APP_SECRET')),
            'app_id' => env('REVERB_APP_ID', env('PUSHER_APP_ID')),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER', 'mt1'),
                'host' => env('REVERB_HOST', env('PUSHER_HOST', '127.0.0.1')),
                'port' => (int) env('REVERB_PORT', env('PUSHER_PORT', 8080)),
                'scheme' => env('REVERB_SCHEME', env('PUSHER_SCHEME', 'http')),
                'useTLS' => env('REVERB_SCHEME', env('PUSHER_SCHEME', 'http')) === 'https',
            ],
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],

    ],

];
