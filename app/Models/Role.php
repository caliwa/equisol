<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;
use App\Contracts\AuditableInterface;
use App\Observers\AuditObserver;
use App\Traits\AuditableTrait;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


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

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.permission'),
            config('permission.table_names.role_has_permissions'),
            config('permission.column_names.role_pivot_key') ?: 'role_id',
            config('permission.column_names.permission_pivot_key') ?: 'permission_id'
        )->using(RolePermission::class);
    }
}