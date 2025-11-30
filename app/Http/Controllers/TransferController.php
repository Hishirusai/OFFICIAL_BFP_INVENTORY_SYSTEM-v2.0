<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Station;
use App\Models\ActivityLog; // ✅ Import
use Illuminate\Support\Facades\Auth; // ✅ Import
use Illuminate\Support\Facades\DB;
use App\Notifications\TransferredNotification;

class TransferController extends Controller
{
    public function store(Request $request, Station $station)
    {
        // 1. Normalize Input
        if ($request->has('item_id') && $request->has('quantity')) {
            $request->merge([
                'transfers' => [
                    [
                        'item_id' => $request->item_id,
                        'quantity' => $request->quantity
                    ]
                ]
            ]);
        }

        $request->validate([
            'to_station_id' => 'required|exists:stations,id',
            'transfers'     => 'required|array|min:1',
            'transfers.*.item_id'  => 'required|exists:items,id',
            'transfers.*.quantity' => 'required|integer|min:1',
            'notes'         => 'nullable|string',
        ]);

        if ($request->to_station_id == $station->id) {
            return back()->with('error', 'Cannot transfer items to the same station.');
        }

        $toStation = Station::find($request->to_station_id);
        $isBulk = count($request->transfers) > 1; 
        $actionType = $isBulk ? 'Bulk Transfer' : 'Single Transfer';

        // <--- CHANGE 1: Initialize the summary array here
        $transferredItemsSummary = []; 

        DB::beginTransaction();
        try {
            foreach ($request->transfers as $transferData) {
                
                $itemToTransfer = Item::where('id', $transferData['item_id'])
                                      ->where('station_id', $station->id)
                                      ->lockForUpdate()
                                      ->firstOrFail();

                if ($itemToTransfer->quantity < $transferData['quantity']) {
                    throw new \Exception("Insufficient quantity for item: {$itemToTransfer->name}");
                }

                // Transfer Logic
                $itemToTransfer->decrement('quantity', $transferData['quantity']);

                $destinationItem = Item::where('station_id', $toStation->id)
                    ->where('product_code', $itemToTransfer->product_code)
                    ->first();

                if ($destinationItem) {
                    $destinationItem->increment('quantity', $transferData['quantity']);
                    $destinationItem->update(['total_cost' => $destinationItem->quantity * $destinationItem->unit_cost]);
                } else {
                    $newItem = $itemToTransfer->replicate();
                    $newItem->station_id = $toStation->id;
                    $newItem->quantity = $transferData['quantity'];
                    $newItem->total_cost = $transferData['quantity'] * $itemToTransfer->unit_cost;
                    $newItem->save();
                }

                // <--- CHANGE 2: Save item details to the array for the receipt
                $transferredItemsSummary[] = [
                    'product_code' => $itemToTransfer->product_code,
                    'name'         => $itemToTransfer->name,
                    'quantity'     => $transferData['quantity'],
                    'unit'         => $itemToTransfer->unit,
                    
                    // ✅ ADDED THIS: Save the cost so we can show it in the modal later
                    'unit_cost'    => $itemToTransfer->unit_cost, 
                    'total_cost'   => $transferData['quantity'] * $itemToTransfer->unit_cost, 
                ];

                // Log Activity
                ActivityLog::create([
                    'user_id'     => Auth::id(),
                    'action_type' => $actionType,
                    'details'     => "Transferred {$transferData['quantity']} {$itemToTransfer->unit} of '{$itemToTransfer->name}' to {$toStation->name}."
                ]);
            }

            // <--- CHANGE 3: Actually SEND the notification
            $toStation->notify(new TransferredNotification(
                $station,                 // From Station
                $transferredItemsSummary, // The list of items we collected
                Auth::user(),             // Who did it
                $request->notes           // Notes
            ));

            DB::commit();
            return redirect()->route('stations.show', $station->id)->with('success', 'Transfer completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Transfer failed: ' . $e->getMessage());
        }
    }
}