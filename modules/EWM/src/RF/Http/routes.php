<?php
use Illuminate\Support\Facades\Route;

// RF Login / Device registration
Route::post('/login', 'LoginController@login');
Route::post('/logout', 'LoginController@logout');

// Inbound RF Operations
Route::prefix('inbound')->group(function() {
    Route::get('/gr/{deliveryId}', 'InboundRFController@getGoodsReceiptDelivery');
    Route::post('/gr/confirm', 'InboundRFController@confirmGoodsReceiptItem');
    Route::post('/putaway/task/{taskId}/confirm', 'InboundRFController@confirmPutawayTask');
});

// Outbound RF Operations
Route::prefix('outbound')->group(function() {
    Route::get('/picking/task', 'OutboundRFController@getNextPickingTask');
    Route::post('/picking/task/{taskId}/confirm', 'OutboundRFController@confirmPickingTaskItem');
    Route::post('/packing/hu/{huId}/add', 'OutboundRFController@packItemToHU');
});

// Internal RF Operations
Route::prefix('internal')->group(function() {
    Route::post('/stock-transfer', 'InternalRFController@performStockTransfer');
    Route::get('/pi/count/{piDocId}/item/{itemId}', 'InternalRFController@getPhysicalInventoryItem');
    Route::post('/pi/count/submit', 'InternalRFController@submitPhysicalInventoryCount');
});

// Add more RF routes as needed for other processes like VAS, Queries, etc.
