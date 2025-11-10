<?php

namespace App\Models;

use App\Enums\CompanyDocumentStatusEnum;
use App\Enums\CompanyDocumentTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyDocument extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyDocumentFactory> */
    use HasFactory;

    protected $fillable = [
        'company_id',
        'type',
        'path',
        'status',
        'rejection_reason',
        'submitted_date',
        'validated_by',
        'validated_date',
    ];

    protected $casts = [
        'type' => CompanyDocumentTypeEnum::class,
        'status' => CompanyDocumentStatusEnum::class,
        'submitted_date' => 'date',
        'validated_date' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function isPending(): bool
    {
        return $this->status === CompanyDocumentStatusEnum::PENDIENTE;
    }

    public function isApproved(): bool
    {
        return $this->status === CompanyDocumentStatusEnum::APROBADO;
    }

    public function isRejected(): bool
    {
        return $this->status === CompanyDocumentStatusEnum::RECHAZADO;
    }
}
