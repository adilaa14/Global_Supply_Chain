<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $guarded = [];
    
    protected $casts = [
        'departure_date' => 'datetime',
        'estimated_arrival' => 'datetime',
        'actual_arrival' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function containers(): HasMany
    {
        return $this->hasMany(ShipmentContainer::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ShipmentDocument::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(ShipmentHistory::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(ShipmentStatusLog::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ShipmentNote::class);
    }

    public function tags(): HasMany
    {
        return $this->hasMany(ShipmentTag::class);
    }

    public function originCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'origin_country_id');
    }

    public function destinationCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'destination_country_id');
    }

    public function originPort(): BelongsTo
    {
        return $this->belongsTo(Port::class, 'origin_port_id');
    }

    public function destinationPort(): BelongsTo
    {
        return $this->belongsTo(Port::class, 'destination_port_id');
    }

    public function commodity(): BelongsTo
    {
        return $this->belongsTo(Commodity::class);
    }

    public function vessel(): BelongsTo
    {
        return $this->belongsTo(Vessel::class);
    }
}
