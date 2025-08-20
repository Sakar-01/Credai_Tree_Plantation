<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tree extends Model
{
    protected $fillable = [
        'tree_id',
        'species',
        'height',
        'location_id',
        'landmark_id',
        'plantation_id',
        'location_description',
        'landmark',
        'latitude',
        'longitude',
        'plantation_date',
        'next_inspection_date',
        'photo_path',
        'images',
        'description',
        'plantation_survey_file',
        'planted_by',
        'status',
    ];

    protected $casts = [
        'plantation_date' => 'date',
        'next_inspection_date' => 'date',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'images' => 'array',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function landmark(): BelongsTo
    {
        return $this->belongsTo(Landmark::class);
    }

    public function plantedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'planted_by');
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }

    public function latestInspection(): HasMany
    {
        return $this->inspections()->latest('inspection_date');
    }

    public function plantation(): BelongsTo
    {
        return $this->belongsTo(Plantation::class);
    }
}
