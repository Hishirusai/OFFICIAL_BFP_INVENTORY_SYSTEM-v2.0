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
            'product_code' => $validated['product_code'],
            'name'         => $validated['name'],
            'type'         => $validated['type'],
            'quantity'     => $validated['quantity'],
            'unit'         => $validated['unit'],
            'unit_cost'    => $validated['unit_cost'],
            'date_acquired'=> $validated['date_acquired'],
            'date_expiry'  => $validated['date_expiry'],
            'description'  => $validated['description'] ?? null,
        ]);

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action_type' => 'Item Created',
            'details'     => "Created '{$item->name}' in {$station->name}.",
            'metadata'    => [
                'item_id' => $item->id,
                'station_id' => $station->id,
                'name' => $item->name,
                'product_code' => $item->product_code,
                'type' => $item->type,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit' => $item->unit,
                'unit_cost' => $item->unit_cost,
                'total_cost' => $item->total_cost,
                'date_expiry' => $item->date_expiry
            ]
        ]);

        return redirect()->route('stations.show', $station->id)->with('success', 'Item added successfully!');
    }

    // --- UPDATED UPDATE METHOD ---
    public function update(Request $request, Station $station, Item $item)
    {
        // 1. Validate inputs (MUST ALLOW 0)
        $validated = $request->validate([
            'name'          => 'required|string',
            'type'          => 'required|string',
            'quantity'      => 'required|integer|min:0', // <--- CRITICAL: Allow 0
            'unit'          => 'required|string',
            'unit_cost'     => 'required|numeric|min:0',
            'date_acquired' => 'required|date|before_or_equal:9999-12-31',
            'date_expiry'   => 'required|date|before_or_equal:9999-12-31',
            'description'   => 'nullable|string',
            'condition'     => 'required|string|in:Serviceable,Unserviceable,BER',
        ]);

        // 2. LOGIC: If Quantity is 0 -> HARD DELETE
        if ((int)$validated['quantity'] === 0) {
            
            // Log before deleting so we have a record
            ActivityLog::create([
                'user_id'     => Auth::id(),
                'action_type' => 'Item Depleted',
                'details'     => "Item '{$item->name}' was permanently removed because quantity was set to 0.",
                'metadata'    => [
                    'product_code' => $item->product_code,
                    'name'         => $item->name,
                    'previous_qty' => $item->quantity,
                    'reason'       => 'Quantity updated to 0 manually'
                ]
            ]);

            // HARD DELETE (Bypass SoftDeletes)
            $item->forceDelete(); 

            return redirect()->route('stations.show', $station->id)
                ->with('success', 'Item permanently removed because quantity was set to 0.');
        }

        // 3. LOGIC: Normal Update (Quantity > 0)
        $oldQuantity = $item->quantity;
        $newQuantity = (int)$validated['quantity'];
        $quantityChange = $newQuantity - $oldQuantity;
        
        $totalCost = $validated['quantity'] * $validated['unit_cost'];

        $item->update([
            'name'          => $validated['name'],
            'type'          => $validated['type'],
            'quantity'      => $validated['quantity'],
            'unit'          => $validated['unit'],
            'unit_cost'     => $validated['unit_cost'],
            'total_cost'    => $totalCost,
            'date_acquired' => $validated['date_acquired'],
            'date_expiry'   => $validated['date_expiry'],
            'description'   => $validated['description'],
            'condition'     => $validated['condition'],
        ]);

        // 4. LOGIC: Determine action type based on quantity change
        if ($quantityChange > 0) {
            // Quantity increased -> Stock Added
            $costAdded = $quantityChange * $validated['unit_cost'];
            ActivityLog::create([
                'user_id'     => Auth::id(),
                'action_type' => 'Stock Added',
                'details'     => "Added {$quantityChange} {$validated['unit']} of '{$item->name}' in {$station->name}.",
                'metadata'    => [
                    'item_id' => $item->id,
                    'station_id' => $station->id,
                    'name' => $item->name,
                    'product_code' => $item->product_code,
                    'type' => $item->type,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'unit_cost' => $item->unit_cost,
                    'total_cost' => $item->total_cost,
                    'date_expiry' => $item->date_expiry,
                    'total_cost_after' => $item->total_cost,
                    'cost_added' => $costAdded
                ]
            ]);
        } elseif ($quantityChange < 0) {
            // Quantity decreased -> Stock Deducted
            $costDeducted = abs($quantityChange) * $validated['unit_cost'];
            ActivityLog::create([
                'user_id'     => Auth::id(),
                'action_type' => 'Stock Deducted',
                'details'     => "Deducted " . abs($quantityChange) . " {$validated['unit']} of '{$item->name}' from {$station->name}.",
                'metadata'    => [
                    'item_id' => $item->id,
                    'station_id' => $station->id,
                    'name' => $item->name,
                    'product_code' => $item->product_code,
                    'type' => $item->type,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'unit_cost' => $item->unit_cost,
                    'total_cost' => $item->total_cost,
                    'date_expiry' => $item->date_expiry,
                    'total_cost_after' => $item->total_cost,
                    'cost_deducted' => $costDeducted
                ]
            ]);
        } else {
            // Quantity unchanged -> Item Updated (only if other fields changed)
            ActivityLog::create([
                'user_id'     => Auth::id(),
                'action_type' => 'Item Updated',
                'details'     => "Updated details for '{$item->name}' in {$station->name}.",
                'metadata'    => [
                    'item_id' => $item->id,
                    'changes' => $item->getChanges()
                ]
            ]);
        }

        return redirect()->route('stations.show', $station->id)->with('success', 'Item updated successfully!');
    }

    public function restore($station_id, $item_id)
    {
        $item = Item::withTrashed()->where('id', $item_id)->firstOrFail();
        $item->restore();

        if(Auth::check()){
            ActivityLog::create([
                'user_id'     => Auth::id(),
                'action_type' => 'Item Restored',
                'details'     => "Restored '{$item->name}' to station ID {$station_id}.",
                'metadata'    => [
                    'item_id' => $item->id,
                    'station_id' => $station_id,
                    'name' => $item->name
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Item restored successfully!');
    }

    public function destroy(Station $station, Item $item)
    {
        // This is the "Dispose" button (Soft Delete)
        $metadata = [
            'item_id'      => $item->id,
            'station_id'   => $station->id,
            'product_code' => $item->product_code,
            'name'         => $item->name,
            'quantity'     => $item->quantity,
        ];

        $itemName = $item->name; 
        
        $item->delete(); // Soft Delete

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action_type' => 'Item Disposed',
            'details'     => "Disposed '{$itemName}' from {$station->name}.",
            'metadata'    => $metadata 
        ]);

        return redirect()->route('stations.show', $station->id)->with('success', 'Item disposed (moved to trash).');
    }

    
}