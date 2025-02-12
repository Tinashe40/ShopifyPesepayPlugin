<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Merchant;
use App\Models\Transaction;
use Codevirtus\Payments\Pesepay;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'shopify_store' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|max:3',
            'order_id' => 'required|string',
        ]);

        $merchant = Merchant::where('shopify_store', $request->shopify_store)->first();
        if (!$merchant) {
            return response()->json(['error' => 'Merchant not found'], 404);
        }

        try {
            $pesepay = new Pesepay($merchant->pesepay_integration_key, $merchant->pesepay_encryption_key);
            $pesepay->returnUrl = env('RETURN_URL');
            $pesepay->resultUrl = env('CALLBACK_URL');

            $transaction = $pesepay->createTransaction($request->amount, $request->currency, $request->order_id);
            $response = $pesepay->initiateTransaction($transaction);

            if ($response->success()) {
                Transaction::create([
                    'reference' => $response->referenceNumber(),
                    'shopify_store' => $request->shopify_store,
                    'amount' => $request->amount,
                    'currency' => $request->currency,
                    'order_id' => $request->order_id,
                    'status' => 'PENDING'
                ]);

                return response()->json(['payment_url' => $response->redirectUrl()], 200);
            } else {
                return response()->json(['error' => $response->message()], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Payment initiation failed'], 500);
        }
    }
}
