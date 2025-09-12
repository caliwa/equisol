<?php

namespace App\Observers;

use App\Contracts\AuditableInterface;
use App\Models\AuditEvent;
use Illuminate\Database\Eloquent\Model;

class AuditObserver
{
    
    private function getMetadata(): array
    {
        return [
            'url' => request()?->fullUrl(),
            'method' => request()?->method(),
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * @param Model&AuditableInterface $model
     */
    private function logEvent(Model $model, string $eventType, ?array $oldValues, ?array $newValues): void
    {
        $user = auth()->user();

        AuditEvent::create([
            'auditable_type' => $model::class,
            'auditable_id' => $model->getKey(),
            'event_type' => $eventType,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'user_id' => $user?->getKey(),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'metadata' => $this->getMetadata(),
        ]);
    }

    /**
     * @param Model&AuditableInterface $model
     */

    public function created(Model $model): void
    {
        if($model instanceof AuditableInterface && $model->shouldAudit('created')){
            $this->logEvent($model, 'created', null, $model->getAuditableData());
        }
    }

    /**
     * @param Model&AuditableInterface $model
     */
    public function updated(Model $model): void
    {
        if($model instanceof AuditableInterface && $model->shouldAudit('updated')){
            $this->logEvent($model, 'updated', $model->getOriginalAuditableData(), $model->getAuditableData());
        }
    }
    /**
     * @param Model&AuditableInterface $model
     */
    public function deleted(Model $model): void
    {
        if($model instanceof AuditableInterface && $model->shouldAudit('deleted')){
            $this->logEvent($model, 'deleted', $model->getOriginalAuditableData(), null);
        }
    }
    /**
     * @param Model&AuditableInterface $model
     */
    public function restored(Model $model): void
    {
        if($model instanceof AuditableInterface && $model->shouldAudit('restored')){
            $this->logEvent($model, 'restored', null, $model->getAuditableData());
        }
    }
}
