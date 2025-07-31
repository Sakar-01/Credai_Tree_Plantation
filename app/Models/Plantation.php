<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Plantation extends Model
{
    protected $fillable = [
        'location_description',
        'latitude',
        'longitude',
        'plantation_date',
        'next_inspection_date',
        'description',
        'plantation_survey_file',
        'created_by',
    ];

    protected $casts = [
        'plantation_date' => 'date',
        'next_inspection_date' => 'date',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
