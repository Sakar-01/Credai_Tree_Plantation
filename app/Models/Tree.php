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
        'location_id',
        'location_description',
        'landmark',
        'latitude',
        'longitude',
        'plantation_date',
        'next_inspection_date',
        'photo_path',
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
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
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
}
