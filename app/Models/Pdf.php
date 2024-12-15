<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pdf extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'file_path',
        'file_link',
        'mime_type',
        'description',
        'uploaded_by',
    ];
    public function pdfable()
    {
        return $this->morphTo();
    }
}
