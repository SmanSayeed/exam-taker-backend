<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Student extends  Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    // protected $guard_name = 'student-api';

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'profile_image',
        'ip_address', 'country', 'country_code', 'address', 'active_status'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function answers()
    {
        return $this->hasMany(Answer::class, 'student_id');
    }

    public function examinations()
    {
        return $this->hasMany(Examination::class, 'created_by');
    }
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
