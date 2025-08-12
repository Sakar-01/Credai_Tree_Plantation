<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Landmark extends Model
{
    protected $fillable = [
        'name',
        'description',
        'location_id',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}