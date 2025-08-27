<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProviderRate extends Model
{
    protected $fillable = ['rate_provider_id', 'weight_kg', 'zone', 'price'];

    public function provider() {
        return $this->belongsTo(RateProvider::class, 'rate_provider_id');
    }
}