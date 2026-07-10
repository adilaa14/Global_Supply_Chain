<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ActivityTimeline extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'company_id',
        'type',
        'title',
        'description',
        'meta_data',
    ];

    protected function casts(): array
    {
        return [
            'meta_data' => 'array',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
