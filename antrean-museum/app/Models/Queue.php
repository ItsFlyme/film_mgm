<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    protected $fillable = [
        'queue_number',
        'visitor_name',
        'visitor_type',
        'institution_name',
        'movie_title',
        'session_time',
        'status',
    ];
}
