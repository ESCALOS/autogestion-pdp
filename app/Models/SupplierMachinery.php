<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class SupplierMachinery extends Model
{
    /** @use HasFactory<\Database\Factories\SupplierMachineryFactory> */
    use HasFactory;

    protected $table = 'supplier_machinery';

    protected $fillable = [
        'supplier_id',
        'license_plate',
        'status',
        'nationality',
        'is_internal',
        'truck_type',
        'has_bonus',
        'tare',
        'appeal_token',
        'appeal_token_expires_at',
    ];

    protected $casts = [
        'status' => \App\Enums\EntityStatusEnum::class,
        'truck_type' => \App\Enums\MachineryUnitTypeEnum::class,
        'is_internal' => 'boolean',
        'has_bonus' => 'boolean',
        'appeal_token_expires_at' => 'datetime',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
