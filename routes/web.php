<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\AdminController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|


Route::get('/', function () {
    return view('welcome');
});
*/
Route::post('/payment/initiate', [PaymentController::class, 'initiatePayment']);
Route::get('/return', [PaymentController::class, 'returnHandler']);
Route::get('/callback', [PaymentController::class, 'callbackHandler']);
Route::post('/save-keys', [MerchantController::class, 'saveKeys']);
Route::post('/pay', [PaymentController::class, 'initiatePayment']);
Route::middleware(['auth.shopify'])->group(function () {
    Route::get('/settings', [AdminController::class, 'showSettings'])->name('admin.settings');
    Route::post('/settings', [AdminController::class, 'saveSettings']);
});