<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultSubject extends Model
{
    protected $fillable = [
        'result_id',
        'subject',
        'full_marks',
        'obtained_marks',
        'grade',
    ];

    public function result()
    {
        return $this->belongsTo(Result::class);
    }
}