<?php

namespace App\Traits;

use App\Models\AuditEvent;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait AuditableTrait
{
    public function auditEvents(): MorphMany
    {
        return $this->morphMany(AuditEvent::class, 'auditable');
    }

    public function getAuditableFields(): array
    {
        return property_exists($this, 'auditableFields') 
        ? $this->auditableFields
        : array_keys($this->getAttributes());
    }

    public function getAuditableEvents(): array
    {
        return property_exists($this, 'auditableEvents')
            ? $this->auditableEvents
            : ['created', 'updated', 'deleted', 'restored'];
    }

    public function shouldAudit(string $event): bool
    {
        return in_array($event, $this->getAuditableEvents());
    }

    public function getAuditableData(): array
    {
        $fields = $this->getAuditableFields();
        return collect($this->getAttributes())
            ->only($fields)
            ->toArray();
    }

    public function getOriginalAuditableData(): array
    {
        $fields = $this->getAuditableFields();
        return collect($this->getOriginal())
            ->only($fields)
            ->toArray();
    }
}
