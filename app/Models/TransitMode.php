<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class TransitMode extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function origins(): BelongsToMany
    {
        return $this->belongsToMany(Origin::class)
                    ->using(OriginTransitMode::class)
                    ->withPivot('days')
                    ->withTimestamps();
    }
}
