<?php

namespace App\Course\Entities\Models;

use App\Course\IO\Database\Factories\CourseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'courses';

    protected $fillable = [
        'course',
    ];

    protected static function newFactory()
    {
        return CourseFactory::new();
    }
}
