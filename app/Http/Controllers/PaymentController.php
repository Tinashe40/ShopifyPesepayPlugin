<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Merchant;
use Codevirtus\Payments\Pesepay;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Initiate a payment transaction
     */
    public function initiatePayment(Request $request)
    {
        // Validate request input
        $request->validate([
            'shopify_store' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|max:3',
            'order_id' => 'required|string',
        ]);

        // Fetch merchant details from database
        $merchant = Merchant::where('shopify_store', $request->shopify_store)->first();
        if (!$merchant) {
            Log::error('Merchant not found', ['shopify_store' => $request->shopify_store]);
            return response()->json(['error' => 'Merchant not found'], 404);
        }

        try {
            // Initialize Pesepay with merchant credentials
            $pesepay = new Pesepay($merchant->pesepay_integration_key, $merchant->pesepay_encryption_key);
            $pesepay->returnUrl = env('RETURN_URL'); // Redirect URL after payment
            $pesepay->resultUrl = env('CALLBACK_URL'); // Webhook for payment confirmation

            // Create and initiate transaction
            $transaction = $pesepay->createTransaction($request->amount, $request->currency, $request->order_id);
            $response = $pesepay->initiateTransaction($transaction);

            if ($response->success()) {
                // Store transaction in the database
                Transaction::create([
                    'reference' => $response->referenceNumber(),
                    'shopify_store' => $request->shopify_store,
                    'amount' => $request->amount,
                    'currency' => $request->currency,
                    'order_id' => $request->order_id,
                    'status' => 'PENDING'
                ]);

                return response()->json([
                    'payment_url' => $response->redirectUrl(),
                    'reference' => $response->referenceNumber()
                ], 200);
            } else {
                Log::error('Pesepay transaction initiation failed', ['message' => $response->message()]);
                return response()->json(['error' => $response->message()], 400);
            }
        } catch (\Exception $e) {
            Log::error('Payment initiation error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Payment initiation failed'], 500);
        }
    }
}