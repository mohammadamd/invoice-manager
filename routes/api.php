<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\WorkerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1/worker', 'middleware' => 'authorize'], function() {
    Route::get('/financial-history', [WorkerController::class, 'getFinancialHistory']);
});

Route::group(['prefix' => 'internal', 'middleware' => 'ipWhitelist:status-manager'], function() {
    Route::post('/calculate-invoice', [InvoiceController::class, 'calculateInvoice']);
    Route::post('/settle-invoice', [InvoiceController::class, 'settleInvoice']);
});
