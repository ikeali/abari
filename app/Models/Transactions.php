<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_reference',
        'virtual_account_number',
        'principal_amount',
        'settled_amount',
        'fee_charged',
        'transaction_date',
        'customer_identifier',
        'transaction_indicator',
        'currency',
        'channel',
        'sender_name',
        'remarks',
        'meta',
        'amount',
        'gateway_ref',
        'email',
        'transaction_status',
        'transaction_type'
        
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'meta' => 'array',
    ];
}
