<?php

namespace App\Models;

use App\Models\TransitMode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Origin extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    /**
     * Un origen puede estar asociado a muchos servicios.
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function transitModes(): BelongsToMany
    {
        return $this->belongsToMany(TransitMode::class)
                    ->withPivot('days')
                    ->withTimestamps();
    }
}
