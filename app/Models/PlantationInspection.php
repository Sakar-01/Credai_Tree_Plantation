<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlantationInspection extends Model
{
    protected $fillable = [
        'plantation_id',
        'inspection_date',
        'description',
        'images',
        'next_inspection_date',
        'overall_health',
        'trees_inspected',
        'healthy_trees',
        'unhealthy_trees',
        'recommendations',
        'inspected_by',
    ];

    protected $casts = [
        'inspection_date' => 'date',
        'next_inspection_date' => 'date',
        'images' => 'array',
    ];

    public function plantation(): BelongsTo
    {
        return $this->belongsTo(Plantation::class);
    }

    public function inspectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }
}
