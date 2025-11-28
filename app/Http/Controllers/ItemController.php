<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Station;
use App\Models\ActivityLog; // ✅ Import
use Illuminate\Support\Facades\Auth; // ✅ Import
use Carbon\Carbon;

class ItemController extends Controller
{
    public function store(Request $request, Station $station)
    {
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
        ]);

        $totalCost = $validated['quantity'] * $validated['unit_cost'];
        $condition = (new Carbon($validated['date_expiry']))->isPast() ? 'Unserviceable' : 'Serviceable';

        $item = Item::create([
            'station_id' => $station->id,
            'total_cost' => $totalCost,
            'condition'  => $condition,
            ...$validated 
        ]);

        // 📝 LOG: Creation
        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action_type' => 'Item Created',
            'details'     => "Created item '{$item->name}' ({$item->quantity} {$item->unit}) in {$station->name}."
        ]);

        return redirect()->route('stations.show', $station->id)->with('success', 'Item added successfully!');
    }

    public function update(Request $request, Station $station, Item $item)
    {
        $validated = $request->validate([
            'product_code'  => 'required|string|unique:items,product_code,' . $item->id, 
            'name'          => 'required|string',
            'type'          => 'required|string',
            'quantity'      => 'required|integer|min:1',
            'unit'          => 'required|string',
            'unit_cost'     => 'required|numeric|min:0',
            'date_acquired' => 'required|date|before_or_equal:9999-12-31',
            'date_expiry'   => 'required|date|before_or_equal:9999-12-31',
            'description'   => 'nullable|string', 
        ]);
        
        // Calculate Changes for Logging
        $oldQty = $item->quantity;
        $newQty = $validated['quantity'];
        $qtyDiff = $newQty - $oldQty;
        
        $totalCost = $newQty * $validated['unit_cost'];
        $condition = (new Carbon($validated['date_expiry']))->isPast() ? 'Unserviceable' : 'Serviceable';

        $item->update([
            'total_cost' => $totalCost,
            'condition'  => $condition,
            ...$validated
        ]);

        // 📝 LOG: Smart Update Logic
        if ($qtyDiff > 0) {
            $action = 'Stock Added';
            $details = "Added {$qtyDiff} {$item->unit} to '{$item->name}'. New Total: {$newQty}.";
        } elseif ($qtyDiff < 0) {
            $action = 'Stock Deducted';
            $details = "Removed " . abs($qtyDiff) . " {$item->unit} from '{$item->name}'. Remaining: {$newQty}.";
        } else {
            $action = 'Item Updated';
            $details = "Updated details for '{$item->name}' (Code: {$item->product_code}).";
        }

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action_type' => $action,
            'details'     => $details
        ]);

        return redirect()->route('stations.show', $station->id)->with('success', 'Item updated successfully!');
    }

    public function destroy(Station $station, Item $item)
    {
        $itemName = $item->name; // Capture name before delete
        $item->delete(); 

        // 📝 LOG: Disposal
        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action_type' => 'Item Disposed',
            'details'     => "Disposed '{$itemName}' from {$station->name}. (Available for Restore)"
        ]);

        return redirect()->route('stations.show', $station->id)->with('success', 'Item disposed of successfully! (It can be restored)');
    }
}