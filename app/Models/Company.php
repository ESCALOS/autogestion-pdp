<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CompanyStatusEnum;
use App\Enums\CompanyTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Company extends Model
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
        'type' => CompanyTypeEnum::class,
        'status' => CompanyStatusEnum::class,
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
