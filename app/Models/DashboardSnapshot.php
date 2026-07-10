<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DashboardSnapshot extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'company_id',
        'snapshot_date',
        'total_revenue',
        'net_profit',
        'total_shipments',
        'country_performance',
        'commodity_performance',
    ];

    protected function casts(): array
    {
        return [
            'snapshot_date' => 'date',
            'total_revenue' => 'decimal:2',
            'net_profit' => 'decimal:2',
            'country_performance' => 'array',
            'commodity_performance' => 'array',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
