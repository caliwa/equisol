<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rate extends Model
{
    use HasFactory;
    protected $fillable = [
        'service_id',
        'weight_tier_id',
        'rate_value'
    ];

    /**
     * Una tarifa pertenece a un servicio específico.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Una tarifa pertenece a un nivel de peso específico.
     */
    public function weightTier(): BelongsTo
    {
        return $this->belongsTo(WeightTier::class);
    }
}
