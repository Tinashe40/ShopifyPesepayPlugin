<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Merchant;
use Codevirtus\Payments\Pesepay;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function initiatePayment(Request $request)
{
    $request->validate([
        'shopify_store' => 'required|string',
        'amount' => 'required|numeric',
        'currency' => 'required|string',
        'order_id' => 'required|string',
    ]);
    Log::info('Shopify Store:', ['store' => $request->shopify_store]);

    $merchant = Merchant::where('shopify_store', $request->shopify_store)->first();

    if (!$merchant) {
        Log::error('Merchant not found for shopify_store:', ['store' => $request->shopify_store]);
        return response()->json(['error' => 'Merchant not found'], 404);
    }

    $pesepay = new Pesepay($merchant->pesepay_integration_key, $merchant->pesepay_encryption_key);
    $pesepay->returnUrl = env('RETURN_URL');
    $pesepay->resultUrl = env('CALLBACK_URL');

    $transaction = $pesepay->createTransaction($request->amount, $request->currency, $request->order_id);
    $response = $pesepay->initiateTransaction($transaction);

    if ($response->success()) {
        return response()->json([
            'payment_url' => $response->redirectUrl(),
            'reference' => $response->referenceNumber()
        ]);
    } else {
        return response()->json(['error' => $response->message()], 400);
    }
}

}