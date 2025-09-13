<?php

namespace App\Models;

use App\Contracts\AuditableInterface;
use App\Observers\AuditObserver;
use App\Traits\AuditableTrait;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[ObservedBy(AuditObserver::class)]
class OriginTransitMode extends Pivot implements AuditableInterface
{
    use AuditableTrait;

    public $incrementing = true;

    protected $table = 'origin_transit_mode';

    protected $auditableFields = ['days', 'origin_id', 'transit_mode_id'];

    protected array $auditableEvents = [
        'created',
        'updated',
        'deleted'
    ];
}