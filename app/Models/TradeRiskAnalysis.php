<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TradeRiskAnalysis extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];
    protected $table = 'trade_risk_analysis';

    public function country() { return $this->belongsTo(Country::class); }
}
