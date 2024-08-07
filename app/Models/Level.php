<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;

    protected $fillable = ['group_id','title', 'details', 'image', 'status'];

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}

