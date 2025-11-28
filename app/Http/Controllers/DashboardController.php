<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Station;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Get all stations for the Dropdown
        $stations = Station::all();

        // 2. Start a query for Items
        $query = Item::query();

        // 3. If a specific station is selected in the dropdown, filter by it
        if ($request->has('station_id') && $request->station_id != '') {
            $query->where('station_id', $request->station_id);
        }

        // 4. Calculate the specific analytics you requested
        // Using clone to keep the base filter (station) intact for each calculation
        
        $totalItems    = (clone $query)->sum('quantity');
        
        $serviceable   = (clone $query)->where('condition', 'Serviceable')->sum('quantity');
        
        $unserviceable = (clone $query)->where('condition', 'Unserviceable')->sum('quantity');
        
        $ber           = (clone $query)->where('condition', 'BER')->sum('quantity');
        
        $totalValue    = (clone $query)->sum('total_cost');

        // 5. Send data to the view
        return view('dashboard', compact(
            'stations', 
            'totalItems', 
            'serviceable', 
            'unserviceable', 
            'ber', 
            'totalValue'
        ));
    }
}