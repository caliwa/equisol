<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Contracts\AuditableInterface;
use App\Observers\AuditObserver;
use App\Traits\AuditableTrait;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(AuditObserver::class)]
class ProviderZone extends Model implements AuditableInterface
{
    use AuditableTrait;
    protected $fillable = ['rate_provider_id', 'country_name', 'country_code', 'zone'];

    protected $auditableFields = ['rate_provider_id', 'country_name', 'country_code', 'zone'];
    
    protected array $auditableEvents = [
        'created',
        'updated',
        'deleted'
    ];

    public function provider() {
        return $this->belongsTo(RateProvider::class, 'rate_provider_id');
    }
}