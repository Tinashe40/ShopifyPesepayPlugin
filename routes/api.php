<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/payment/initiate', [PaymentController::class, 'initiatePayment']);
Route::post('/webhook', [WebhookController::class, 'handlePesepayWebhook']);