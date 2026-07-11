<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VesselRoute extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'departure_time' => 'datetime',
        'estimated_arrival' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function vessel()
    {
        return $this->belongsTo(Vessel::class);
    }

    public function originPort()
    {
        return $this->belongsTo(Port::class, 'origin_port_id');
    }

    public function destinationPort()
    {
        return $this->belongsTo(Port::class, 'destination_port_id');
    }
}
