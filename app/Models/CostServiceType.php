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
class CostServiceType extends Model implements AuditableInterface
{
    use HasFactory, AuditableTrait;
    
    protected $fillable = ['name'];

    protected $auditableFields = ['name'];

    protected array $auditableEvents = [
        'created',
        'updated',
        'deleted'
    ];

    /**
     * Un tipo de servicio puede estar en muchos servicios.
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function weightTiers()
    {
        return $this->hasMany(WeightTier::class);
    }

    public function costItems(): HasMany
    {
        return $this->hasMany(CostItem::class);
    }
}