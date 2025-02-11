<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Merchant;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function showSettings()
    {
        $shopifyStore = Auth::user()->shopify_domain; // Get merchant's store
        $merchant = Merchant::where('shopify_store', $shopifyStore)->first();

        return view('admin.settings', compact('merchant'));
    }

    public function saveSettings(Request $request)
    {
        $request->validate([
            'pesepay_integration_key' => 'required|string',
            'pesepay_encryption_key' => 'required|string',
        ]);

        $shopifyStore = Auth::user()->shopify_domain;
        $merchant = Merchant::updateOrCreate(
            ['shopify_store' => $shopifyStore],
            [
                'pesepay_integration_key' => $request->pesepay_integration_key,
                'pesepay_encryption_key' => $request->pesepay_encryption_key,
            ]
        );

        return redirect()->back()->with('success', 'Settings saved successfully.');
    }
}