<?php

namespace App\Assignment\Entities\Models;

use App\Assignment\IO\Database\Factories\AssignmentFactory;
use App\Course\Entities\Models\Course;
use App\Department\Entities\Models\Department;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'department_id',
        'course_id',
        'duration',
        'instruction',
        'due_date',
        'due_time',
        'file_path',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    protected static function newFactory(): AssignmentFactory
    {
        return AssignmentFactory::new();
    }
}
