<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WeightTier extends Model
{
    use HasFactory;
    protected $fillable = ['service_type_id', 'label', 'min_weight', 'display_order'];

    /**
     * Un nivel de peso tiene muchas tarifas asociadas (una por cada servicio).
     */
    public function rates(): HasMany
    {
        return $this->hasMany(Rate::class);
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }
}