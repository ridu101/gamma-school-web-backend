<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = [
        'student_id',
        'exam_name',
        'gpa',
        'full_marks',
        'total_marks',
        'passed',
    ];

    protected $casts = [
        'gpa' => 'decimal:2',
        'passed' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subjects()
    {
        return $this->hasMany(ResultSubject::class);
    }
}