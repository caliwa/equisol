<?php

namespace App\Models;

use App\Contracts\AuditableInterface;
use App\Observers\AuditObserver;
use App\Traits\AuditableTrait;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[ObservedBy(AuditObserver::class)]
class RolePermission extends Pivot implements AuditableInterface
{
    use AuditableTrait;

    public $incrementing = true;

    protected $table = 'role_has_permissions';

    protected $auditableFields = ["permission_id", "role_id"];
    
    protected array $auditableEvents = [
        'created',
        'deleted'
    ];
}