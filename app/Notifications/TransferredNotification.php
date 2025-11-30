<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Station;
use App\Models\User;

class TransferredNotification extends Notification
{
    use Queueable;

    protected $fromStation;
    protected $itemsSummary;
    protected $user;
    protected $notes;

    public function __construct($fromStation, $itemsSummary, $user, $notes)
    {
        $this->fromStation = $fromStation;
        $this->itemsSummary = $itemsSummary;
        $this->user = $user;
        $this->notes = $notes;
    }

    public function via($notifiable)
    {
        return ['database']; // This saves it to your new table
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Stock Received', // ✅ Changed from "Incoming Transfer"
            'message' => "Items have been added to inventory from " . $this->fromStation->name, // ✅ Past tense
            'from_station_name' => $this->fromStation->name,
            'from_station_location' => $this->fromStation->location,
            'user_name' => $this->user->name ?? 'System',
            'transfer_date' => now()->toDateTimeString(),
            'notes' => $this->notes,
            'items' => $this->itemsSummary,
        ];
    }
}