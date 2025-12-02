<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Station;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Get all stations for the Dropdown
        $stations = Station::all();

        // 2. Start a query for Items
        $query = Item::query();

        // 3. If a specific station is selected, filter by it
        if ($request->has('station_id') && $request->station_id != '') {
            $query->where('station_id', $request->station_id);
        }

        // 4. Card Analytics
        // Using clone to keep the base filter (station) intact
        $totalItems    = (clone $query)->sum('quantity');
        $serviceable   = (clone $query)->where('condition', 'Serviceable')->sum('quantity');
        $unserviceable = (clone $query)->where('condition', 'Unserviceable')->sum('quantity');
        $ber           = (clone $query)->where('condition', 'BER')->sum('quantity');
        $totalValue    = (clone $query)->sum('total_cost');

        // 5. CHART DATA LOGIC -----------------------------------------
        // so basically, prepare the data para sa chart
        // A. Get available years using PHP (Database Agnostic)
        // We fetch all dates, parse them in PHP to find unique years.
        // This prevents SQL errors (like "no such function: MONTH")
        $allDates = (clone $query)->pluck('date_acquired');
        
        $availableYears = $allDates
            ->map(function ($date) {
                return $date ? Carbon::parse($date)->format('Y') : null;
            })
            ->filter()
            ->unique()
            ->sortDesc() // Latest years first (2025, 2024...)
            ->values();

        // B. Determine which year to show, yung dropdown selected year or default there
        // Ito mga conditions:
        // If user selects a year, use it.
        // If not, default to the most recent year available.
        // If DB is empty, default to current year.
        $defaultYear = $availableYears->first() ?? date('Y');
        $selectedYear = $request->input('year', $defaultYear);

        // Initialize arrays with 0 for all 12 months (Jan-Dec)
        $chartData = [
            'Serviceable'   => array_fill(0, 12, 0),
            'Unserviceable' => array_fill(0, 12, 0),
            'BER'           => array_fill(0, 12, 0),
        ];

        // Fetch items for the SELECTED year
        $itemsForChart = (clone $query)
            ->whereYear('date_acquired', $selectedYear)
            ->get(['date_acquired', 'condition', 'quantity']);

        foreach ($itemsForChart as $item) {
            if (!$item->date_acquired) continue;

            // Parse date using Carbon to get month (1 = Jan, 12 = Dec)
            $month = Carbon::parse($item->date_acquired)->month;
            $monthIndex = $month - 1; // 0-indexed for array
            
            $condition = $item->condition;
            $qty = (int) $item->quantity;

            // Add quantity to the correct month/condition bucket
            if (isset($chartData[$condition])) {
                $chartData[$condition][$monthIndex] += $qty;
            }
        }

        // 6. Send data to the view
        return view('dashboard', compact(
            'stations', 
            'totalItems', 
            'serviceable', 
            'unserviceable', 
            'ber', 
            'totalValue',
            'chartData',
            'availableYears', 
            'selectedYear'
        ));
    }
}