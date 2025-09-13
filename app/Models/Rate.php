<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Contracts\AuditableInterface;
use App\Observers\AuditObserver;
use App\Traits\AuditableTrait;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(AuditObserver::class)]
class Rate extends Model implements AuditableInterface
{
    use HasFactory, AuditableTrait;
    
    protected $fillable = [
        'service_id',
        'weight_tier_id',
        'rate_value'
    ];

    protected $auditableFields = [
        'service_id',
        'weight_tier_id',
        'rate_value'
    ];

    protected array $auditableEvents = [
        'created',
        'updated',
        'deleted'
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
