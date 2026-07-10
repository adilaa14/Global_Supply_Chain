<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class GlobalAlert extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'category',
        'severity',
        'title',
        'message',
        'location',
        'lat',
        'lng',
        'impact_score',
        'affected_entities',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'decimal:6',
            'lng' => 'decimal:6',
            'impact_score' => 'integer',
            'affected_entities' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
