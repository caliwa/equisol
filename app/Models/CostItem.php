<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CostItem extends Model
{
    use HasFactory;

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
        'variable_rate',
        'minimum_charge',
        'currency_id',
        'calculation_type',
        'formula_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fixed_amount' => 'decimal:2',
        'variable_rate' => 'decimal:4',
        'minimum_charge' => 'decimal:2',
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
