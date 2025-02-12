<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handlePesepayWebhook(Request $request)
    {
        Log::info('Pesepay Webhook Received:', $request->all());

        $request->validate([
            'reference_number' => 'required|string',
            'status' => 'required|string',
        ]);

        $transaction = Transaction::where('reference', $request->reference_number)->first();

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        $transaction->status = $request->status;
        $transaction->save();

        return response()->json(['message' => 'Webhook processed successfully']);
    }
}