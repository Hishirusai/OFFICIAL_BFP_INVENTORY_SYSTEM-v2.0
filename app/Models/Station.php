<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable; // ✅ 1. Import this

class Station extends Model
{
    // ✅ 2. Add Notifiable to the use statement
    use HasFactory, SoftDeletes, Notifiable; 

    protected $fillable = [
        'name',
        'location',
    ];

    /**
     * Get the items for the station.
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}