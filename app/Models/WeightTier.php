<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Contracts\AuditableInterface;
use App\Observers\AuditObserver;
use App\Traits\AuditableTrait;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(AuditObserver::class)]
class WeightTier extends Model implements AuditableInterface
{
    use HasFactory, AuditableTrait;
    protected $fillable = ['service_type_id', 'label', 'min_weight', 'display_order'];

    protected $auditableFields = ['service_type_id', 'label', 'min_weight', 'display_order'];

    protected array $auditableEvents = ['created', 'updated', 'deleted'];

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