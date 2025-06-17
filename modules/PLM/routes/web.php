<?php

use Illuminate\Support\Facades\Route;

// Most PLM UI will be via Vue.js SPA, so web routes might be minimal.
Route::get('/plm-info', function () {
    return "ARCA PLM Module Information Page (Web Route)";
});
EOL
