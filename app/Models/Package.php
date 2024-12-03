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
        'img',
        'discount',
        'discount_type',
        'duration_days',
    ];

    // Relationships
    public function packageCategory()
    {
        return $this->hasOne(PackageCategory::class);
    }

    public function student_payment()
    {
        return $this->hasMany(StudentPayment::class);
    }

    public function subscribers()
    {
        return $this->hasManyThrough(Student::class, Subscription::class, 'package_id', 'id', 'id', 'student_id');
    }

    public function modelTests()
    {
        return $this->hasMany(ModelTest::class);
    }

    // Local scope for active packages
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
