<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    // ✅ 1. Add 'metadata' to fillable
    protected $fillable = ['user_id', 'action_type', 'details', 'metadata'];

    // ✅ 2. Cast metadata as an array so we can access it easily
    protected $casts = [
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}