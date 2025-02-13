<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WebhookController;

Route::prefix('v1')->group(function () {

    // Payment initiation endpoint
    Route::post('/payment/initiate', [PaymentController::class, 'initiatePayment']);

    // Pesepay Webhook endpoint
    Route::post('/webhook', [WebhookController::class, 'handlePesepayWebhook']);

});