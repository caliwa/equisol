<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class AuditEvent extends Model
{
    protected $fillable = [
        'auditable_type',
        'auditable_id',
        'event_type',
        'old_values',
        'new_values',
        'user_id',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'metadata' => 'array',
        ];
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function changedFields(): Attribute
    {
        return Attribute::make(
            get: function(): array {
                if(!$this->old_values || !$this->new_values){
                    return [];
                }

                return array_keys(array_diff_assoc(
                    $this->old_values,
                    $this->new_values
                ));
            }
        );
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function forModel($query, Model $model)
    {
        return $query->where('auditable_type', get_class($model))
                     ->where('auditable_id', $model->getKey());
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function ofType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }
    
}
