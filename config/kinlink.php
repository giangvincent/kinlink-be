<?php

return [
    'exports' => [
        'disk' => env('KINLINK_EXPORT_DISK', env('FILESYSTEM_DISK', 'local')),
    ],
    'media' => [
        'allowed_models' => [
            App\Models\Person::class,
            App\Models\Post::class,
        ],
        'aliases' => [
            'person' => App\Models\Person::class,
            'post' => App\Models\Post::class,
        ],
    ],
];
