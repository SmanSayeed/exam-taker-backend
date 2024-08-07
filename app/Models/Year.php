<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'details', 'image', 'status'];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
