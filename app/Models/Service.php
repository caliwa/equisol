<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;
    protected $fillable = [
        'origin_id',
        'service_type_id',
        'currency_id',
        'minimum_charge'
    ];

    /**
     * Un servicio pertenece a un origen.
     */
    public function origin(): BelongsTo
    {
        return $this->belongsTo(Origin::class);
    }

    /**
     * Un servicio pertenece a un tipo de servicio.
     */
    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    /**
     * Un servicio PUEDE pertenecer a una moneda (es opcional).
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Un servicio tiene muchas tarifas (una por cada nivel de peso).
     */
    public function rates(): HasMany
    {
        return $this->hasMany(Rate::class);
    }
}