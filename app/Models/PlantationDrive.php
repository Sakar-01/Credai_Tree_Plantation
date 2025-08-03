<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlantationDrive extends Model
{
    protected $fillable = [
        'drive_id',
        'title',
        'description',
        'location_id',
        'number_of_trees',
        'images',
        'plantation_date',
        'next_inspection_date',
        'latitude',
        'longitude',
        'plantation_survey_file',
        'created_by',
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

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function trees(): HasMany
    {
        return $this->hasMany(Tree::class);
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }
}
