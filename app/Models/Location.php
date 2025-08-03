<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $fillable = [
        'name',
        'landmark',
        'description',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function trees(): HasMany
    {
        return $this->hasMany(Tree::class);
    }

    public function getTreeCountAttribute()
    {
        return $this->trees()->count();
    }

    public function getLatestPlantationDateAttribute()
    {
        return $this->trees()->max('plantation_date');
    }

    public function getSamplePhotoAttribute()
    {
        return $this->trees()->whereNotNull('photo_path')->first()?->photo_path;
    }
}
