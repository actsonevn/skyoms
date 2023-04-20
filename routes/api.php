<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StockController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Auth Controller
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/sign-up', [AuthController::class, 'signUp']);

Route::middleware('auth:sanctum')->get('/user/detail/{id}', [AuthController::class, 'detailUser']);
// Route::get('/user/detail/{id}', [AuthController::class, 'detailUser']);

// Dashboard Controller
Route::middleware('auth:sanctum')->get('/overview', [DashboardController::class, 'getOverviewDashboard']);
Route::middleware('auth:sanctum')->get('/customer/orders', [DashboardController::class, 'getCustomerOrderList']);
Route::middleware('auth:sanctum')->get('/customer/orders/hours', [DashboardController::class, 'getOrderByHours']);


Route::post('/stock/create', [StockController::class, 'createNewStockDevide']);
Route::get('/stock/list', [StockController::class, 'getStockList']);


