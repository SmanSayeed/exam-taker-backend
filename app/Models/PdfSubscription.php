<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdfSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'pdf_id',
        'student_id',
        'package_id',
        'is_active',
        'expires_at',
    ];
}
