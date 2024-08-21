<?php

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WakicallbackController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/waki-callback', [WakicallbackController::class, 'actionGetwakidata'])->name('report.actionGetwakidata');

Route::get('/arpu-data', [ApiController::class, 'ArpuData'])->name('api.arpu.data');
Route::get('/arpu-data-usd', [ApiController::class, 'ArpuDataUsd'])->name('api.arpu.data.usd');

Route::get('/linkit-reconcileData', [ApiController::class, 'reconcileData'])->name('api.reconciledata.data');

Route::post('user/email',[ApiController::class, 'useremail']);


