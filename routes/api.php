<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\PaymentController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });use App\Http\Controllers\MerchantController;


Route::post('/save-keys', [MerchantController::class, 'saveKeys']);
Route::post('/pay', [PaymentController::class, 'initiatePayment']);
Route::get('/return', [PaymentController::class, 'returnHandler']);
Route::get('/callback', [PaymentController::class, 'callbackHandler']);
Route::post('/payment/webhook', [PaymentController::class, 'paymentWebhook']);