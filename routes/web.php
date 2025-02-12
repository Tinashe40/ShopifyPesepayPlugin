<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingsController;

// Shopify Settings Page
Route::get('/shopify/settings', [SettingsController::class, 'index']);
Route::post('/shopify/settings/save', [SettingsController::class, 'saveSettings']);