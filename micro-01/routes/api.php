<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CompanyController;
use Illuminate\Support\Facades\Route;

Route::apiResource('companies', CompanyController::class);
Route::apiResource('categories', CategoryController::class);

Route::get('/', static function () {
    return response()->json(['message' => 'success']);
});
