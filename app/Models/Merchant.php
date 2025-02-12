<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    use HasFactory;
    protected $table = 'merchants';
    protected $fillable = [
        'shopify_store',
        'pesepay_integration_key',
        'pesepay_encryption_key',
    ];
    public $timestamps = true;
}