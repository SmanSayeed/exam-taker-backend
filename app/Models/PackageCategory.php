<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'section_id',
        'exam_type_id',
        'exam_sub_type_id',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
