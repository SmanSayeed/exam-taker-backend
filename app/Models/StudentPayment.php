<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'student_id',
        'payment_method',
        'mobile_number',
        'amount',
        'coupon',
        'transaction_id',
        'verified',
        'verified_at',
        'package_id'
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
