<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vessel extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    public function shippingLine()
    {
        return $this->belongsTo(ShippingLine::class);
    }

    public function positions()
    {
        return $this->hasMany(VesselPosition::class)->orderBy('timestamp', 'desc');
    }

    public function latestPosition()
    {
        return $this->hasOne(VesselPosition::class)->latestOfMany('timestamp');
    }

    public function routes()
    {
        return $this->hasMany(VesselRoute::class);
    }

    public function activeRoute()
    {
        return $this->hasOne(VesselRoute::class)->where('is_active', true);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function events()
    {
        return $this->hasMany(TrackingEvent::class);
    }
}
