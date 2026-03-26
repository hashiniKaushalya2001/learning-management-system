<?php

namespace App\Students\Entities\Models;

use App\Students\IO\Database\Factories\StudentsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Students extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'birthday', 'nic', 'phone_number', 'department',
    ];

    protected static function newFactory(): StudentsFactory
    {
        return StudentsFactory::new();
    }
}
