<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File; // Required for File facade

// Example: Include sub-module API routes if they exist and are enabled
// Note: This dynamic inclusion is illustrative. A more robust mechanism might be needed.
// The LscmServiceProvider's loadModuleRoutes method is a more typical place for this.

Route::get('/lscm-status-check', function () {
    return response()->json(['module' => 'LSCM Main', 'status' => 'active_via_api_route']);
});

// Example of how sub-module routes could be included from here,
// though typically ServiceProvider handles this.
//  = ['MM', 'SD', 'PP', 'PM', 'QM'];
// foreach ($subModules as $subModule) {
//     if (config('lscm.' . strtolower($subModule) . '.enabled', false)) {
//         $filePath = base_path('modules/LSCM/src/' . $subModule . '/routes/api.php');
//         if (File::exists($filePath)) {
//             Route::group(['prefix' => strtolower($subModule)], function() use ($filePath, $subModule) {
//                 require $filePath;
//             });
//         }
//     }
// }
