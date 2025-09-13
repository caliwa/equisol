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
class Currency extends Model implements AuditableInterface
{
    use HasFactory, AuditableTrait;

    protected $table = 'currencies_master';

    protected $fillable = ['code', 'name', 'value'];

    protected $auditableFields = ['code', 'name', 'value'];

    protected array $auditableEvents = [
        'created',
        'updated',
        'deleted'
    ];

    /**
     * Una moneda puede ser usada en muchos servicios.
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function costItems(): HasMany
    {
        return $this->hasMany(CostItem::class);
    }
}