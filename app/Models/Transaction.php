<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'source_id',
        'booked_date',
        'trade_date',
        'settlement_date',
        'portfolio',
        'transaction_type',
        'security_name',
        'isin',
        'quantity',
        'price',
        'interest',
        'total_fees',
        'fees_currency',
        'amount',
        'amount_currency',
        'purchase_value',
        'purchase_value_currency',
        'result',
        'result_currency',
        'total_quantity',
        'balance',
        'exchange_rate',
        'transaction_text',
        'cancellation_date',
        'contract_note_number',
        'verification_number',
        'brokerage',
        'brokerage_currency',
        'currency_rate',
        'initial_interest',
    ];
}
