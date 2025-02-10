<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Merchant;
use Illuminate\Support\Facades\Log;

class MerchantController extends Controller
{
    public function saveKeys(Request $request)
    {
        Log::info('Received Request:', $request->all());

        $request->validate([
            'shopify_store' => 'required|string',
            'pesepay_integration_key' => 'required|string',
            'pesepay_encryption_key' => 'required|string',
        ]);

        try {
            Merchant::updateOrCreate(
                ['shopify_store' => $request->shopify_store],
                [
                    'pesepay_integration_key' => $request->pesepay_integration_key,
                    'pesepay_encryption_key' => $request->pesepay_encryption_key,
                ]
            );

            return response()->json(['message' => 'Keys saved successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error saving keys: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }
}