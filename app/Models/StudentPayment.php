<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'payment_method',
        'mobile_number',
        'amount',
        'transaction_id',
        'verified',
        'verified_at'
    ];
}
