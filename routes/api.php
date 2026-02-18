<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\MarketingDataController;
use App\Http\Controllers\MarketingLogController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProjectLogController;
use App\Http\Controllers\QuoteMasterController;
use App\Http\Controllers\QuoteRequestController;
use App\Http\Controllers\SessionLogsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;

Route::get('/customers/search', [CustomerController::class, 'apiSearch']);


Route::apiResource('quote-request', QuoteRequestController::class);
Route::apiResource('quote-master', QuoteMasterController::class);
// Route::apiResource('project-logs', ProjectLogController::class);
Route::apiResource('documents', DocumentController::class);
Route::apiResource('permissions', PermissionController::class);
Route::apiResource('marketing-log', MarketingLogController::class);
// Route::apiResource('marketing-data', MarketingDataController::class);
Route::apiResource('session-logs', SessionLogsController::class);
Route::apiResource('settings', SettingsController::class);
Route::apiResource('user', UserController::class);
Route::get('/', function () {
    return view('welcome');
});
