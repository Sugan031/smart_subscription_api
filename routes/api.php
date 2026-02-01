<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UsageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, "register"]);
Route::post("/login", [AuthController::class, "login"]);

Route::middleware('auth:api')->group(function () {
    Route::get("/plans", [PlanController::class, 'index']);
    Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
    Route::get("/subscription", [SubscriptionController::class, 'show']);
    Route::post('/usage/consume', [UsageController::class, 'consume']);
    Route::get('/usage/stats', [UsageController::class, 'stats']);
    Route::post('/subscription/changePlan', [SubscriptionController::class, 'changePlan']);
});
