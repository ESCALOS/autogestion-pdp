<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chassis extends Model
{
    /** @use HasFactory<\Database\Factories\ChassisFactory> */
    use HasFactory;

    protected $fillable = [
        'company_id',
        'license_plate',
        'status',
        'vehicle_type',
        'axle_count',
        'has_bonus',
        'tare',
        'safe_weight',
        'height',
        'length',
        'width',
        'is_insulated',
        'material',
        'accepts_20ft',
        'accepts_40ft',
        'appeal_token',
        'appeal_token_expires_at',
    ];

    protected $casts = [
        'status' => \App\Enums\EntityStatusEnum::class,
        'has_bonus' => 'boolean',
        'is_insulated' => 'boolean',
        'accepts_20ft' => 'boolean',
        'accepts_40ft' => 'boolean',
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
