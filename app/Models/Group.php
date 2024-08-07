<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'details', 'image', 'status'];

    public function levels()
    {
        return $this->hasMany(Level::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }
}
