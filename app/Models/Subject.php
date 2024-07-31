<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['level_id', 'group_id', 'part', 'title', 'details', 'image', 'status'];

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
