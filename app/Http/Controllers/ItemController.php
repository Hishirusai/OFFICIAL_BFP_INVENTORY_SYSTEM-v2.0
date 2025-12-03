<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Station;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    public function store(Request $request, Station $station)
    {
        // STORE needs validation because it's a new item
        $validated = $request->validate([
            'product_code'  => 'required|string|unique:items,product_code',
            'name'          => 'required|string',
            'type'          => 'required|string',
            'quantity'      => 'required|integer|min:1',
            'unit'          => 'required|string',
            'unit_cost'     => 'required|numeric|min:0',
            'date_acquired' => 'required|date|before_or_equal:9999-12-31',
            'date_expiry'   => 'required|date|before_or_equal:9999-12-31',
            'description'   => 'nullable|string',
        ], [], [], 'addItem'); 

        $totalCost = $validated['quantity'] * $validated['unit_cost'];
        $condition = (new Carbon($validated['date_expiry']))->isPast() ? 'Unserviceable' : 'Serviceable';

        $item = Item::create([
            'station_id' => $station->id,
            'total_cost' => $totalCost,
            'condition'  => $condition,
            ...$validated 
        ]);

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action_type' => 'Item Created',
            'details'     => "Created item '{$item->name}' ({$item->quantity} {$item->unit}) in {$station->name}.",
            // ✅ SAVE ALL DETAILS
            'metadata'    => [
                'product_code' => $item->product_code,
                'name'         => $item->name,
                'type'         => $item->type,
                'quantity'     => $item->quantity,
                'unit'         => $item->unit,
                'unit_cost'    => $item->unit_cost,
                'total_cost'   => $item->total_cost,
                'condition'    => $item->condition,
                'date_acquired'=> $item->date_acquired,
                'date_expiry'  => $item->date_expiry,
                'description'  => $item->description,
            ]
        ]);

        return redirect()->route('stations.show', $station->id)->with('success', 'Item added successfully!');
    }

    public function update(Request $request, Station $station, Item $item)
    {
        // ✅ FIXED: Removed 'product_code' from validation entirely.
        // We are not updating the code, so we don't check it. No more "Already Taken" error.
        $validated = $request->validate([
            'name'          => 'required|string',
            'type'          => 'required|string',
            'quantity'      => 'required|integer|min:1',
            'unit'          => 'required|string',
            'unit_cost'     => 'required|numeric|min:0',
            'date_acquired' => 'required|date|before_or_equal:9999-12-31',
            'date_expiry'   => 'nullable|date|before_or_equal:9999-12-31',
            'description'   => 'nullable|string', 
            'condition'     => 'required|string',
        ]);
        
        // 1. Find the Item using the ID from the Hidden Form Input
        // This is safer than relying on the URL parameter
        $targetItem = Item::findOrFail($request->item_id);

        // 2. Calculations
        $oldQty = $targetItem->quantity;
        $newQty = $validated['quantity'];
        $qtyDiff = $newQty - $oldQty;
        
        $totalCost = $newQty * $validated['unit_cost'];
        $condition = (new Carbon($validated['date_expiry']))->isPast() ? 'Unserviceable' : $validated['condition'];

        // 3. Update
        // Only fields in $validated are updated. Product Code is NOT touched.
        $targetItem->update([
            'total_cost' => $totalCost,
            'condition'  => $condition,
            ...$validated
        ]);

        // 4. Logging
        $metadata = [
            'product_code' => $targetItem->product_code,
            'name'         => $targetItem->name,
            'quantity'     => $newQty, // Use new quantity
            'unit'         => $targetItem->unit,
            'unit_cost'    => $validated['unit_cost'],
            'total_cost'   => $totalCost,
            'condition'    => $condition,
            'date_expiry'  => $validated['date_expiry'] ?? null,
        ];

        if ($qtyDiff > 0) {
            $action = 'Stock Added';
            $details = "Added {$qtyDiff} {$targetItem->unit} to '{$targetItem->name}'. New Total: {$newQty}.";
            // ✅ Stock Added: Total cost (after addition) and Cost added
            $metadata['total_cost_after'] = $totalCost; // Total cost of items now
            $metadata['cost_added'] = $qtyDiff * $validated['unit_cost']; // How much is being added
        } elseif ($qtyDiff < 0) {
            $action = 'Stock Deducted';
            $details = "Removed " . abs($qtyDiff) . " {$targetItem->unit} from '{$targetItem->name}'. Remaining: {$newQty}.";
            // ✅ Stock Deducted: Total cost after deduction and Cost being deducted
            $metadata['total_cost_after'] = $totalCost; // Total cost after deduction
            $metadata['cost_deducted'] = abs($qtyDiff) * $validated['unit_cost']; // Cost being deducted
        } else {
            $action = 'Item Updated';
            $details = "Updated details for '{$targetItem->name}' (Code: {$targetItem->product_code}).";
        }

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action_type' => $action,
            'details'     => $details,
            'metadata'    => $metadata
        ]);

        return redirect()->route('stations.show', $station->id)->with('success', 'Item updated successfully!');
    }

    public function destroy(Station $station, Item $item)
    {
        $itemName = $item->name; 
        $item->delete(); 

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action_type' => 'Item Disposed',
            'details'     => "Disposed '{$itemName}' from {$station->name}. (Available for Restore)"
        ]);

        return redirect()->route('stations.show', $station->id)->with('success', 'Item disposed of successfully! (It can be restored)');
    }
}