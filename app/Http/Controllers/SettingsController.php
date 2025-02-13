<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Merchant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Show the settings page for the merchant.
     */
    public function index()
    {
        $shopifyStore = request()->query('shop'); // Shopify store URL
        $merchant = Merchant::where('shopify_store', $shopifyStore)->first();

        return view('settings', compact('merchant', 'shopifyStore'));
    }

    /**
     * Save or update PesePay credentials.
     */
    public function saveSettings(Request $request)
{
    Log::info('Saving PesePay Settings:', $request->all());

    $request->validate([
        'shopify_store' => 'required|string',
        'pesepay_integration_key' => 'required|string',
        'pesepay_encryption_key' => 'required|string',
    ]);

    try {
        // Store or update merchant API keys
        Merchant::updateOrCreate(
            ['shopify_store' => $request->shopify_store],
            [
                'pesepay_integration_key' => $request->pesepay_integration_key,
                'pesepay_encryption_key' => $request->pesepay_encryption_key,
            ]
        );

        return redirect()->back()->with('success', 'PesePay settings saved successfully!');
    } catch (\Exception $e) {
        Log::error('Error saving PesePay settings', ['error' => $e->getMessage()]);
        return redirect()->back()->with('error', 'Failed to save settings.');
    }
}

}