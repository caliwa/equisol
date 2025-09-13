<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Contracts\AuditableInterface;
use App\Observers\AuditObserver;
use App\Traits\AuditableTrait;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(AuditObserver::class)]
class ProviderRate extends Model implements AuditableInterface
{
    use AuditableTrait;

    protected $fillable = ['rate_provider_id', 'weight_kg', 'zone', 'price'];
    
    protected $auditableFields = ['rate_provider_id', 'weight_kg', 'zone', 'price'];

    protected array $auditableEvents = [
        'created',
        'updated',
        'deleted'
    ];

    public function provider() {
        return $this->belongsTo(RateProvider::class, 'rate_provider_id');
    }
}