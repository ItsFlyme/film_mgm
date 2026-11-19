<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MuseumAd extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image_path',
        'duration_seconds',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'duration_seconds' => 'integer',
            'sort_order' => 'integer',
        ];
    }
}
