<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DashboardMetric extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'company_id',
        'metric_key',
        'numeric_value',
        'string_value',
        'json_value',
        'calculated_at',
    ];

    protected function casts(): array
    {
        return [
            'numeric_value' => 'decimal:2',
            'json_value' => 'array',
            'calculated_at' => 'datetime',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
