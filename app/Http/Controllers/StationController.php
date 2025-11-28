<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Station;
use App\Models\Item;
use App\Models\ActivityLog; // ✅ Add this line
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StationController extends Controller
{
    // AUTOCOMPLETE (Unchanged)
    public function autocomplete(Request $request)
    {
        $query = $request->get('query');
        $items = Item::select('product_code', 'name', 'type')
                    ->distinct()
                    ->where('product_code', 'LIKE', "%{$query}%")
                    ->orWhere('name', 'LIKE', "%{$query}%")
                    ->orWhere('type', 'LIKE', "%{$query}%")
                    ->limit(10)
                    ->get();
        return response()->json($items);
    }

    // INDEX FUNCTION (Updated)
    public function index(Request $request)
    {
        $stationsQuery = Station::query();
        $totalMatchedQuantity = 0;
        $isSearchingItems = false;
        $searchDisplay = '';

        // CHECK 1: Is there a hidden strict code? (User clicked dropdown)
        if ($request->filled('strict_code_search')) {
            $code = $request->strict_code_search;
            $isSearchingItems = true;
            $searchDisplay = $request->item_search; // What the user typed/saw

            // Exact match on Product Code only
            $searchLogic = function ($query) use ($code) {
                $query->where('product_code', '=', $code); 
            };
            
            $stationsQuery->whereHas('items', $searchLogic);
            $stationsQuery->withSum(['items as matched_quantity' => $searchLogic], 'quantity');

        // CHECK 2: Standard text search (User typed and pressed Enter without clicking suggestion)
        } elseif ($request->filled('item_search')) {
            $search = $request->item_search;
            $isSearchingItems = true;
            $searchDisplay = $search;

            $searchLogic = function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('product_code', 'LIKE', "%{$search}%")
                      ->orWhere('name', 'LIKE', "%{$search}%")
                      ->orWhere('type', 'LIKE', "%{$search}%");
                });
            };

            $stationsQuery->whereHas('items', $searchLogic);
            $stationsQuery->withSum(['items as matched_quantity' => $searchLogic], 'quantity');
            
        } else {
            // No search
            $stationsQuery->withSum('items as matched_quantity', 'quantity');
        }

        $stations = $stationsQuery->get();

        if ($isSearchingItems || $stations->isNotEmpty()) {
            $totalMatchedQuantity = $stations->sum('matched_quantity');
        }

        return view('stations.index', compact('stations', 'totalMatchedQuantity', 'isSearchingItems', 'searchDisplay'));
    }

    // SHOW FUNCTION (Updated to handle strict code)
    public function show(Request $request, Station $station)
    {
        $query = $station->items()->latest();

        // Check Strict Code first
        if ($request->filled('strict_code_search')) {
             $query->where('product_code', '=', $request->strict_code_search);
        } 
        // Fallback to standard search
        elseif ($request->filled('search') || $request->filled('item_search')) {
            $searchTerm = $request->get('search', $request->get('item_search'));
            $query->where(function($q) use ($searchTerm) {
                $q->where('product_code', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('type', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Filters
        if ($request->filled('condition')) {
            if ($request->condition == 'BER') {
                $query->where(function($q) { $q->where('condition', '=', 'B.E.R.')->orWhere('condition', '=', 'BER'); });
            } else {
                $query->where('condition', '=', $request->condition);
            }
        }
        if ($request->filled('unit')) {
            $query->where('unit', 'LIKE', "%{$request->unit}%");
        }

        $items = $query->paginate(11)->appends($request->all());
        $itemNames = Item::distinct()->pluck('name');
        $itemTypes = Item::distinct()->pluck('type');

        return view('stations.show', compact('station', 'items', 'itemNames', 'itemTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|unique:stations,name',
            'location' => 'required|string', 
        ], [
            'name.required' => 'Please enter a station name.',
            'name.unique'   => 'This station name already exists.',
            'location.required' => 'Please enter a location.',
        ]);

        $station = Station::create($validated);

        // 📝 LOG: Station Creation
        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action_type' => 'Station Created',
            'details'     => "Created new station: '{$station->name}' in {$station->location}."
        ]);

        return redirect()->route('stations.index')->with('success', 'Station created successfully!');
    }

    public function update(Request $request, Station $station)
    {
        $validated = $request->validate([
            'name'     => 'required|unique:stations,name,' . $station->id,
            'location' => 'required|string',
        ]);

        $station->update($validated);
        return redirect()->route('stations.index')->with('success', 'Station updated successfully!');
    }

    public function destroy(Station $station)
    {
        if ($station->id == 1) {
            return redirect()->route('stations.index')->with('error', 'You cannot delete the Main Station!');
        }
        $station->delete();
        return redirect()->route('stations.index')->with('success', 'Station deleted successfully!');
    }
}