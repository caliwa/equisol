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
class CostItem extends Model implements AuditableInterface
{
    use HasFactory, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'service_type_id',
        'stage',
        'concept',
        'fixed_amount',
        'currency_id',
        'formula_notes',
        'formula',
    ];

    protected $auditableFields = [
        'service_type_id',
        'stage',
        'concept',
        'fixed_amount',
        'currency_id',
        'formula_notes',
        'formula',
    ];

    protected array $auditableEvents = [
        'created',
        'updated',
        'deleted'
    ];

    protected $casts = [
        'formula' => 'array',
    ];

    /**
     * Get the service type that this cost item belongs to.
     */
    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    /**
     * Get the currency for the cost item.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
}