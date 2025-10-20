<?php

return [
    'offline_mode' => env('POS_OFFLINE_MODE', false),
    'store_code' => env('POS_STORE_CODE', 'STORE15'),
    'terminal_id' => env('POS_TERMINAL_ID', '01'),
    'rth_endpoint' => env('POS_RTH_ENDPOINT', 'http://localhost:8000/api/rth/v1/process-batch'),
];
