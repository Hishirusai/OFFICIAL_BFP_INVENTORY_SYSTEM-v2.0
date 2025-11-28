<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Station;
use App\Models\ActivityLog; // ✅ Import
use Illuminate\Support\Facades\Auth; // ✅ Import
use Illuminate\Support\Facades\DB;

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
        $isBulk = count($request->transfers) > 1; // Check if bulk
        $actionType = $isBulk ? 'Bulk Transfer' : 'Single Transfer';

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

                // 📝 LOG: Log EACH item movement
                ActivityLog::create([
                    'user_id'     => Auth::id(),
                    'action_type' => $actionType,
                    'details'     => "Transferred {$transferData['quantity']} {$itemToTransfer->unit} of '{$itemToTransfer->name}' to {$toStation->name}."
                ]);
            }

            DB::commit();
            return redirect()->route('stations.show', $station->id)->with('success', 'Transfer completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Transfer failed: ' . $e->getMessage());
        }
    }
}