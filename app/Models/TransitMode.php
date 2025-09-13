<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Contracts\AuditableInterface;
use App\Observers\AuditObserver;
use App\Traits\AuditableTrait;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(AuditObserver::class)]
class TransitMode extends Model implements AuditableInterface
{
    use HasFactory, AuditableTrait;

    protected $fillable = ['name'];
    protected $auditableFields = ['name'];

    protected array $auditableEvents = [
        'created',
        'updated',
        'deleted'
    ];

    public function origins(): BelongsToMany
    {
        return $this->belongsToMany(Origin::class)
                    ->withPivot('days')
                    ->withTimestamps();
    }
}
