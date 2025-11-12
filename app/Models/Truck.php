<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Truck extends Model
{
    /** @use HasFactory<\Database\Factories\TruckFactory> */
    use HasFactory;

    protected $fillable = [
        'company_id',
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
        'is_internal' => 'boolean',
        'has_bonus' => 'boolean',
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
}
