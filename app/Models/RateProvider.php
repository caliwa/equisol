<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class RateProvider extends Model
{
    protected $fillable = ['name', 'code'];

    public function zones() {
        return $this->hasMany(ProviderZone::class);
    }
    public function rates() {
        return $this->hasMany(ProviderRate::class);
    }
}