<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plantation extends Model
{
    protected $fillable = [
        'location_id',
        'landmark_id',
        'location_description',
        'landmark',
        'latitude',
        'longitude',
        'plantation_date',
        'tree_count',
        'description',
        'images',
        'created_by',
    ];

    protected $casts = [
        'plantation_date' => 'date',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'images' => 'array',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function landmark(): BelongsTo
    {
        return $this->belongsTo(Landmark::class);
    }

    public function trees(): HasMany
    {
        return $this->hasMany(Tree::class);
    }
}
