<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
    'station_id',
    'product_code',
    'name',
    'type',
    'description',
    'quantity',
    'unit', // ✅ Added
    'unit_cost',
    'total_cost',
    'date_acquired',
    'date_expiry',
    'condition',
];
}
