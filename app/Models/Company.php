<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'company_name',
        'company_type',
        'business_license',
        'tax_number',
        'country_id',
        'city',
        'address',
        'postal_code',
        'phone',
        'email',
        'website',
        'logo',
        'status',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
