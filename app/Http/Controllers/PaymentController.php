<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Merchant;
use Codevirtus\Payments\Pesepay;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

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

    // Fetch merchant details
    $merchant = Merchant::where('shopify_store', $request->shopify_store)->first();
    if (!$merchant) {
        Log::error('Merchant not found', ['shopify_store' => $request->shopify_store]);
        return response()->json(['error' => 'Merchant not found'], 404);
    }

    try {
        // Initialize Pesepay
        $pesepay = new Pesepay($merchant->pesepay_integration_key, $merchant->pesepay_encryption_key);
        $pesepay->returnUrl = env('RETURN_URL');
        $pesepay->resultUrl = env('CALLBACK_URL');

        // Create and initiate transaction
        $transaction = $pesepay->createTransaction($request->amount, $request->currency, $request->order_id);
        $response = $pesepay->initiateTransaction($transaction);

        if ($response->success()) {
            // Store transaction in the database
            $newTransaction = Transaction::create([
                'reference' => $response->referenceNumber(),
                'shopify_store' => $request->shopify_store,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'order_id' => $request->order_id,
                'status' => 'PENDING'
            ]);

            Log::info('Transaction successfully initiated', [
                'reference' => $response->referenceNumber(),
                'shopify_store' => $request->shopify_store,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'order_id' => $request->order_id
            ]);

            // Check if request is from a web browser (redirect user directly)
            if ($request->wantsJson()) {
                return response()->json([
                    'payment_url' => $response->redirectUrl(),
                    'reference' => $response->referenceNumber()
                ], 200);
            } else {
                return redirect()->away($response->redirectUrl());
            }
        } else {
            Log::error('Pesepay transaction initiation failed', [
                'message' => $response->message(),
                'shopify_store' => $request->shopify_store
            ]);
            return response()->json(['error' => $response->message()], 400);
        }
    } catch (\Exception $e) {
        Log::error('Payment initiation error', ['error' => $e->getMessage()]);
        return response()->json(['error' => 'Payment initiation failed'], 500);
    }
}
 

    /*
     * Handle Pesepay webhook (payment confirmation)

    public function paymentWebhook(Request $request)
    {
        Log::info('Webhook received', $request->all());

        // Validate incoming webhook request
        $request->validate([
            'reference_number' => 'required|string',
            'status' => 'required|string|in:PENDING,COMPLETED,FAILED',
        ]);

        // Find the transaction
        $transaction = Transaction::where('reference', $request->reference_number)->first();
        if (!$transaction) {
            Log::error('Transaction not found', ['reference' => $request->reference_number]);
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        // Update transaction status inside a database transaction
        DB::beginTransaction();
        try {
            $transaction->status = $request->status;
            $transaction->save();
            DB::commit();

            Log::info('Transaction updated successfully', [
                'reference' => $transaction->reference,
                'status' => $transaction->status,
            ]);

            return response()->json(['message' => 'Webhook processed successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update transaction', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to process webhook'], 500);
        }
    }
        */
        public function paymentWebhook(Request $request)
{
    Log::info('Webhook received:', $request->all());

    $request->validate([
        'reference_number' => 'required|string',
        'status' => 'required|string',
    ]);

    // Find transaction
    $transaction = Transaction::where('reference', $request->reference_number)->first();

    if (!$transaction) {
        Log::error('Transaction not found:', ['reference' => $request->reference_number]);
        return response()->json(['error' => 'Transaction not found'], 404);
    }

    // Update transaction status
    $transaction->status = $request->status;
    $transaction->save();

    if ($request->status == 'PAID') {
        // Notify Shopify Order API that payment is complete
        // Make API request to Shopify
        $shopifyOrderID = $transaction->order_id;
        $shopifyStore = $transaction->shopify_store;

        $shopifyResponse = Http::withHeaders([
            'X-Shopify-Access-Token' => env('SHOPIFY_ACCESS_TOKEN')
        ])->post("https://$shopifyStore/admin/api/2023-01/orders/$shopifyOrderID/transactions.json", [
            'transaction' => [
                'kind' => 'capture',
                'status' => 'success'
            ]
        ]);

        Log::info('Shopify order updated:', $shopifyResponse->json());
    }

    return response()->json(['message' => 'Payment processed successfully']);
}

}