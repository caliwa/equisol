<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface AuditableInterface
{
    public function getAuditableFields(): array;
    
    public function getAuditableEvents(): array;

    public function shouldAudit(string $event): bool;

    public function auditEvents(): MorphMany;

    public function getAuditableData(): array;

    public function getOriginalAuditableData(): array;
}
