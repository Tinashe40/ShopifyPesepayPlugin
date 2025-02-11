<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Merchant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function showSettings()
    {
        $user = Auth::user();

        if (!$this->isStoreOwner($user)) {
            return redirect()->route('home')->with('error', 'Access denied. Only store owners can modify payment settings.');
        }

        $merchant = Merchant::where('shopify_store', $user->shopify_domain)->first();
        return view('admin.settings', compact('merchant'));
    }

    public function saveSettings(Request $request)
    {
        $user = Auth::user();

        if (!$this->isStoreOwner($user)) {
            return redirect()->route('home')->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'pesepay_integration_key' => 'required|string',
            'pesepay_encryption_key' => 'required|string',
        ]);

        $merchant = Merchant::updateOrCreate(
            ['shopify_store' => $user->shopify_domain],
            [
                'pesepay_integration_key' => $request->pesepay_integration_key,
                'pesepay_encryption_key' => $request->pesepay_encryption_key,
            ]
        );

        return redirect()->back()->with('success', 'Settings saved successfully.');
    }

    private function isStoreOwner($user)
    {
        return $user->is_owner; // Shopify API provides an `is_owner` field
    }
}