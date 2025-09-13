<?php

namespace App\Models;

use App\Models\TransitMode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Contracts\AuditableInterface;
use App\Observers\AuditObserver;
use App\Traits\AuditableTrait;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(AuditObserver::class)]
class Origin extends Model implements AuditableInterface
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
     * Un origen puede estar asociado a muchos servicios.
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function transitModes(): BelongsToMany
    {
        return $this->belongsToMany(TransitMode::class)
                    ->using(OriginTransitMode::class)
                    ->withPivot('days')
                    ->withTimestamps();
    }
}
