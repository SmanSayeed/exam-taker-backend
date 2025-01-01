<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamQuotaSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'mobile_number',
        'payment_method',
        'transaction_id',
        'coupon',
        'verified',
        'verified_at',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
