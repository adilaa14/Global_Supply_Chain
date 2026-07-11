<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingEvent extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'event_time' => 'datetime',
        'location_lat' => 'decimal:8',
        'location_lng' => 'decimal:8',
    ];

    public function vessel()
    {
        return $this->belongsTo(Vessel::class);
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function port()
    {
        return $this->belongsTo(Port::class);
    }
}
