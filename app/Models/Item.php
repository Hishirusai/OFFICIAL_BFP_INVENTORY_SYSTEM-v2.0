<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class Item extends Model
{
    use SoftDeletes; 

    protected $fillable = [
        'station_id',
        'product_code',
        'name',
        'type',
        'description',
        'quantity',
        'unit',
        'unit_cost',
        'total_cost',
        'date_acquired',
        'date_expiry',
        'condition',
    ];
    
    protected $casts = [
        'deleted_at' => 'datetime',
        'quantity' => 'integer', 
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];
}