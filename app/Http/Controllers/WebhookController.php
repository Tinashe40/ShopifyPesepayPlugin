<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class WebhookController extends Controller
{
    /**
     * Handle incoming Pesepay webhook requests.
     */
    public function handlePesepayWebhook(Request $request)
    {
        Log::info('Pesepay Webhook Received', $request->all());

        // Validate webhook request
        $validated = $request->validate([
            'reference_number' => 'required|string',
            'status' => 'required|string',
            'amount' => 'required|numeric',
            'currency' => 'required|string|max:3',
            'order_id' => 'required|string',
            'shopify_store' => 'required|string',
        ]);

        // Find transaction by reference number
        $transaction = Transaction::where('reference', $validated['reference_number'])->first();

        if (!$transaction) {
            Log::error('Transaction not found', ['reference' => $validated['reference_number']]);
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        // Update transaction status
        $transaction->status = strtoupper($validated['status']);
        $transaction->save();

        // Log success
        Log::info('Transaction Updated Successfully', [
            'reference' => $transaction->reference,
            'status' => $transaction->status
        ]);

        // Optionally, notify the merchant or update Shopify order status
        // Example: send a notification or update order status via Shopify API
        $this->notifyMerchant($transaction);

        return response()->json(['message' => 'Webhook processed successfully']);
    }

    /**
     * Notify the merchant about payment status (Optional)
     */
    private function notifyMerchant($transaction)
    {
        try {
            // Example: Send a notification email or update Shopify order
            Log::info('Notifying merchant about transaction', [
                'shopify_store' => $transaction->shopify_store,
                'reference' => $transaction->reference,
                'status' => $transaction->status,
                'amount' => $transaction->amount,
                'currency' => $transaction->currency,
                'order_id' => $transaction->order_id
            ]);

            // You can implement Shopify API call to update order status if needed.
        } catch (\Exception $e) {
            Log::error('Failed to notify merchant', ['error' => $e->getMessage()]);
        }
    }
}