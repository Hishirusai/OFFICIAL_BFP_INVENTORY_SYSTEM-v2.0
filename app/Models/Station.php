<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany; // ✅ Added this import

class Station extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'location',
    ];

    /**
     * Get the items for the station.
     */
    public function items(): HasMany // ✅ THIS IS THE MISSING FUNCTION
    {
        return $this->hasMany(Item::class);
    }
}