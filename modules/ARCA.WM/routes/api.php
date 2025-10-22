<?php

use Illuminate\Support\Facades\Route;

Route::prefix('wm')->group(function () {
    // Warehouse Master Data
    Route::prefix('master-data')->group(function () {
        // Routes for Warehouse, Zone, Bin, etc. will be defined here
    });

    // Inbound Operations
    Route::prefix('inbound')->group(function () {
        // Routes for Goods Receipt, Putaway, etc. will be defined here
    });

    // Outbound Operations
    Route::prefix('outbound')->group(function () {
        // Routes for Goods Issue, Picking, Packing, etc. will be defined here
    });

    // Internal Movements
    Route::prefix('internal')->group(function () {
        // Routes for Stock Transfer, Bin Reallocation, etc. will be defined here
    });

    // Inventory Counting
    Route::prefix('inventory')->group(function () {
        // Routes for Cycle Counting, Physical Inventory, etc. will be defined here
    });
});
