<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    use HasFactory;

    // Explicitly define the table name (optional)
    protected $table = 'merchants';

    // Fields that can be mass assigned
    protected $fillable = [
        'shopify_store',
        'pesepay_integration_key',
        'pesepay_encryption_key',
    ];

    // Enable timestamps (created_at, updated_at)
    public $timestamps = true;
}