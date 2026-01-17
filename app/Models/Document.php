<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    /** @use HasFactory<\Database\Factories\DocumentFactory> */
    use HasFactory;

    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'type',
        'path',
        'submitted_date',
        'course_date',
        'expiration_date',
        'status',
        'rejection_reason',
        'validated_by',
        'validated_date',
    ];

    protected $casts = [
        'type' => \App\Enums\DocumentTypeEnum::class,
        'status' => \App\Enums\DocumentStatusEnum::class,
        'submitted_date' => 'date',
        'course_date' => 'date',
        'expiration_date' => 'date',
        'validated_date' => 'datetime',
    ];

    public function documentable()
    {
        return $this->morphTo();
    }

    public function validator()
    {
        return $this->belongsTo(\App\Models\User::class, 'validated_by');
    }

    /**
     * Scope para documentos vencidos que necesitan actualización.
     */
    public function scopeExpiredAndNeedsUpdate($query)
    {
        return $query->where('expiration_date', '<', now())
            ->where('status', \App\Enums\DocumentStatusEnum::APPROVED);
    }

    /**
     * Scope para documentos próximos a vencer en una fecha específica.
     */
    public function scopeExpiringOnDate($query, string $date)
    {
        return $query->where('expiration_date', $date)
            ->where('status', \App\Enums\DocumentStatusEnum::APPROVED);
    }
}
