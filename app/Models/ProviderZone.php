<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProviderZone extends Model
{
    protected $fillable = ['rate_provider_id', 'country_name', 'country_code', 'zone'];

    public function provider() {
        return $this->belongsTo(RateProvider::class, 'rate_provider_id');
    }
}