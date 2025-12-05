<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable; // ✅ 1. Import this
use Illuminate\Support\Facades\DB;

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

    /**
     * Get unread notifications for a specific user
     */
    public function getUnreadNotificationsForUser($userId)
    {
        $readNotificationIds = DB::table('notification_reads')
            ->where('user_id', $userId)
            ->pluck('notification_id')
            ->toArray();

        return $this->notifications()
            ->whereNotIn('id', $readNotificationIds)
            ->get();
    }

    /**
     * Check if a user has read a specific notification
     */
    public function hasUserReadNotification($userId, $notificationId)
    {
        return DB::table('notification_reads')
            ->where('user_id', $userId)
            ->where('notification_id', $notificationId)
            ->exists();
    }
}