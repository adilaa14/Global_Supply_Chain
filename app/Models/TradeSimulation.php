<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TradeSimulation extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    public function user() { return $this->belongsTo(User::class); }
    public function originCountry() { return $this->belongsTo(Country::class, 'origin_country_id'); }
    public function destinationCountry() { return $this->belongsTo(Country::class, 'destination_country_id'); }
    public function commodity() { return $this->belongsTo(Commodity::class); }
}
