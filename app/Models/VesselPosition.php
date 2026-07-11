<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VesselPosition extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'timestamp' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'speed' => 'decimal:2',
    ];

    public function vessel()
    {
        return $this->belongsTo(Vessel::class);
    }
}
