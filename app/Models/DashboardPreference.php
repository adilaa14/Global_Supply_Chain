<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DashboardPreference extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'layout_id',
        'visible_widgets',
        'favorite_countries',
        'favorite_commodities',
        'favorite_routes',
        'default_filters',
    ];

    protected function casts(): array
    {
        return [
            'visible_widgets' => 'array',
            'favorite_countries' => 'array',
            'favorite_commodities' => 'array',
            'favorite_routes' => 'array',
            'default_filters' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
