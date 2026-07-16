<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Country extends Model
{
    use HasUuids;
    protected $guarded = [];

    public function economy() { return $this->hasOne(CountryEconomy::class); }
    public function risk() { return $this->hasOne(CountryRisk::class); }
    public function opportunity() { return $this->hasOne(CountryOpportunity::class); }
    public function ranking() { return $this->hasOne(CountryRanking::class); }
    public function tradeStatistics() { return $this->hasMany(CountryTradeStatistic::class); }
    public function tradeAgreements() { return $this->hasMany(CountryTradeAgreement::class); }
    public function regulations() { return $this->hasMany(CountryRegulation::class); }
    public function ports() { return $this->hasMany(Port::class); }
    public function userFavorites() { return $this->hasMany(UserFavorite::class); }
}
