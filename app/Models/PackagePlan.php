<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackagePlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'duration_days',
        'price',
        'is_active',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
