<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TradeOpportunity extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    public function country() { return $this->belongsTo(Country::class); }
    public function commodity() { return $this->belongsTo(Commodity::class); }
}
