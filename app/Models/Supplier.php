<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CompanyStatusEnum;
use App\Enums\CompanyTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Supplier extends Model
{
    /** @use HasFactory<\Database\Factories\SupplierFactory> */
    use HasFactory;

    protected $fillable = [
        'type',
        'ruc',
        'business_name',
        'status',
        'is_active',
        'appeal_token',
        'appeal_token_expires_at',
        'phone_prefix',
        'phone_number',
        'phone_country',
        'verification_code',
        'verification_code_expires_at',
        'phone_verified_at',
    ];

    protected $casts = [
        'type' => CompanyTypeEnum::class,
        'status' => CompanyStatusEnum::class,
        'appeal_token_expires_at' => 'datetime',
        'verification_code_expires_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(SupplierUser::class);
    }

    public function representative()
    {
        return $this->hasOne(SupplierUser::class)->where('is_supplier_representative', true);
    }

    public function drivers()
    {
        return $this->hasMany(SupplierDriver::class);
    }

    public function machinery()
    {
        return $this->hasMany(SupplierMachinery::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function generateVerificationCode(): string
    {
        $code = mb_str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->update([
            'verification_code' => $code,
            'verification_code_expires_at' => now()->addMinutes(10),
        ]);

        return $code;
    }

    public function verifyPhoneCode(string $code): bool
    {
        if ($this->verification_code !== $code) {
            return false;
        }

        if ($this->verification_code_expires_at && now()->isAfter($this->verification_code_expires_at)) {
            return false;
        }

        $this->update([
            'phone_verified_at' => now(),
            'verification_code' => null,
            'verification_code_expires_at' => null,
        ]);

        return true;
    }

    public function getFullPhoneNumber(): string
    {
        return $this->phone_prefix.$this->phone_number;
    }
}
