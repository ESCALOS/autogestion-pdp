<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CompanyStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

final class SupplierUser extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\SupplierUserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'supplier_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'dni',
        'name',
        'lastname',
        'email',
        'password',
        'supplier_id',
        'is_supplier_representative',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function supplierIsActive(): bool
    {
        return $this->supplier?->is_active;
    }

    public function supplierStatus(): CompanyStatusEnum
    {
        return $this->supplier?->status;
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->name} {$this->lastname}";
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
