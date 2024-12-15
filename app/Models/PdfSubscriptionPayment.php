<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdfSubscriptionPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'pdf_id',
        'amount',
        'payment_method',
        'mobile_number',
        'transaction_id',
        'coupon',
        'verified',
        'expires_at',
    ];
}
