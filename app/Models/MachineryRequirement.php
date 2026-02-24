<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MachineryOperationTypeEnum;
use App\Enums\MachineryUnitTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class MachineryRequirement extends Model
{
    /** @use HasFactory<\Database\Factories\MachineryRequirementFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cost_center',
        'vessel_name',
        'operation_type',
        'unit_type',
        'units_quantity',
        'activation_time',
        'days_quantity',
        'announcement_launched_at',
        'approval_status',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'operation_type' => MachineryOperationTypeEnum::class,
        'unit_type' => MachineryUnitTypeEnum::class,
        'units_quantity' => 'integer',
        'days_quantity' => 'integer',
        'activation_time' => 'datetime',
        'announcement_launched_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isActivationTimeLocked(): bool
    {
        return $this->activation_time && $this->activation_time < now();
    }

    public function isUnitsQuantityEditable(): bool
    {
        return $this->announcement_launched_at === null;
    }

    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->approval_status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }

    public function canBeEdited(): bool
    {
        return $this->isPending();
    }
}
