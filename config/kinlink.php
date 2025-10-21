<?php

return [
    'exports' => [
        'disk' => env('KINLINK_EXPORT_DISK', env('FILESYSTEM_DISK', 'local')),
    ],
];
