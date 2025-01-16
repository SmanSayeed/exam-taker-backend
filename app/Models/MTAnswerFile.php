<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MTAnswerFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'exam_id',
        'file_url',
        'original_filename',
        'mime_type',
        'file_size'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function examination()
    {
        return $this->belongsTo(Examination::class, 'exam_id');
    }
}
