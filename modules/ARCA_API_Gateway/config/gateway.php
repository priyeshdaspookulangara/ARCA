<?php

return [
    'modules' => [
        'pos' => env('POS_MODULE_URL', 'http://localhost/api/pos'),
        'mm' => env('MM_MODULE_URL', 'http://localhost/api/mm'),
        'fina' => env('FINA_MODULE_URL', 'http://localhost/api/fina'),
        // Add other module mappings here.
    ],
];
