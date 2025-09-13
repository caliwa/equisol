<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;
use App\Contracts\AuditableInterface;
use App\Observers\AuditObserver;
use App\Traits\AuditableTrait;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(AuditObserver::class)]
class Role extends SpatieRole implements AuditableInterface
{
    use HasFactory, AuditableTrait;

    protected $fillable = ['name', 'guard_name', 'description'];
    protected $auditableFields = ['name', 'guard_name', 'description'];

    protected array $auditableEvents = [
        'created',
        'updated',
        'deleted'
    ];
    
}