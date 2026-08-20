<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'file',
        'publish_date',
        'is_active',
    ];

    protected $casts = [
        'publish_date' => 'date',
        'is_active' => 'boolean',
    ];
}