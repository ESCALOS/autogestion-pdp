<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    /** @use HasFactory<\Database\Factories\DriverFactory> */
    use HasFactory;

    protected $fillable = [
        'company_id',
        'document_type',
        'document_number',
        'name',
        'lastname',
        'license_number',
        'status',
        'appeal_token',
        'appeal_token_expires_at',
    ];

    protected $casts = [
        'document_type' => \App\Enums\DriverDocumentTypeEnum::class,
        'status' => \App\Enums\EntityStatusEnum::class,
        'appeal_token_expires_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function getFullNameAttribute()
    {
        return $this->name . ' ' . $this->lastname;
    }
}
