<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AlternativeDestination extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    public function originalDestination() { return $this->belongsTo(Country::class, 'original_destination_id'); }
    public function alternativeCountry() { return $this->belongsTo(Country::class, 'alternative_country_id'); }
    public function commodity() { return $this->belongsTo(Commodity::class); }
}
