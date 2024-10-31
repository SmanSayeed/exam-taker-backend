<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'price',
        'duration_days',
    ];
    public function packageCategory()
    {
        return $this->hasOne(PackageCategory::class);
    }
}
