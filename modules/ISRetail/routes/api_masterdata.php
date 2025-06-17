<?php

use Illuminate\Support\Facades\Route;

// All routes here are assumed to be under 'api/isretail/masterdata' prefix.

Route::get('/articles/status', function () {
    return response()->json(['domain' => 'Article Master Extensions', 'status' => 'active']);
});

// Example:
// Route::apiResource('generic-articles', 'Article\GenericArticleController');
// Route::apiResource('generic-articles.variants', 'Article\ArticleVariantController')->shallow();
// Route::apiResource('sites-ext', 'Site\SiteExtensionController');
// Route::apiResource('assortments', 'AssortmentListing\AssortmentController');
// Route::apiResource('listing-conditions', 'AssortmentListing\ListingConditionController');
// Route::apiResource('merch-categories', 'MerchCategory\MerchCategoryNodeController');
