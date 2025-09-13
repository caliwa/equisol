<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Contracts\AuditableInterface;
use App\Observers\AuditObserver;
use App\Traits\AuditableTrait;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(AuditObserver::class)]
class RateProvider extends Model implements AuditableInterface
{
    use AuditableTrait;

    protected $fillable = ['name', 'code'];

    protected $auditableFields = ['name', 'code'];

    protected array $auditableEvents = [
        'created',
        'updated',
        'deleted'
    ];

    public function zones() {
        return $this->hasMany(ProviderZone::class);
    }
    public function rates() {
        return $this->hasMany(ProviderRate::class);
    }
}