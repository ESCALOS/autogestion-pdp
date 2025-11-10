<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory;

    protected $fillable = [
        'type',
        'ruc',
        'business_name',
        'status',
        'is_active',
        'appeal_token',
        'appeal_token_expires_at',
    ];

    protected $casts = [
        'appeal_token_expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function representative()
    {
        return $this->hasOne(User::class)->where('is_company_representative', true);
    }

    public function documents()
    {
        return $this->hasMany(CompanyDocument::class);
    }
}
