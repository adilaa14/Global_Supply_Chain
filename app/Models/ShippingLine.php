<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingLine extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    public function vessels()
    {
        return $this->hasMany(Vessel::class);
    }
}
