<?php

namespace App\Meterial\Entities\Models;

use App\Course\Entities\Models\Course;
use App\Department\Entities\Models\Department;
use App\Meterial\IO\Database\Factories\MeterialFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meterial extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'meterials';

    protected $fillable = [
        'department',
        'course_id',
        'meterial',
        'aim',
        'lecturer',
        'semester',
        'duration',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    protected static function newFactory(): MeterialFactory
    {
        return MeterialFactory::new();
    }
}
