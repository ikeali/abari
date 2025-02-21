<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SquadTransaction extends Model
{
    use HasFactory;

    protected $table = 'squad_transactions';

    protected $fillable = [
        'transaction_ref',
        'amount',
        'merchant_amount',
        'currency',
        'transaction_status',
        'account_name',
        'account_number',
        'bank',
        'expected_amount',
        'is_blocked',
        'refund'
    ];


}
