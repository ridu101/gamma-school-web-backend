<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'student_id',
        'name',
        'email',
        'phone',
        'father_name',
        'mother_name',
        'class',
        'section',
        'roll',
        'date_of_birth',
        'address',
        'photo',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'status' => 'boolean',
    ];
}