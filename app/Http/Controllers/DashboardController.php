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
        
        // A. Get available years
        $allDates = (clone $query)->pluck('date_acquired');
        
        // Step 1: Extract years from database items
        $dbYears = $allDates
            ->map(function ($date) {
                return $date ? Carbon::parse($date)->format('Y') : null;
            })
            ->filter();

        // ✅ FIX 1: Always add the Current System Year (e.g., 2026)
        // This ensures the dropdown works even if no items exist for the new year yet.
        $currentYear = date('Y');
        $dbYears->push($currentYear);

        // Step 2: Unique and Sort (Latest first)
        $availableYears = $dbYears
            ->unique()
            ->sortDesc()
            ->values();

        // B. Determine which year to show
        // Use user selection, OR default to current system year if available
        $defaultYear = $availableYears->contains($currentYear) ? $currentYear : $availableYears->first();
        $selectedYear = $request->input('year', $defaultYear);

        // Initialize arrays with 0 for all 12 months (Jan-Dec)
        $chartData = [
            'Serviceable'   => array_fill(0, 12, 0),
            'Unserviceable' => array_fill(0, 12, 0),
            'BER'           => array_fill(0, 12, 0),
            'TotalItems'    => array_fill(0, 12, 0),
            'TotalValue'    => array_fill(0, 12, 0),
        ];

        // ✅ FIX 3: CUMULATIVE LOGIC (Stock History)
        // We calculate the end of the selected year (e.g., Dec 31, 2025)
        $endOfSelectedYear = Carbon::create($selectedYear, 12, 31)->endOfDay();

        // Fetch ALL items acquired on or before the end of this year
        // (Items bought in previous years should still appear in Jan 1st of this year)
        $itemsForChart = (clone $query)
            ->whereDate('date_acquired', '<=', $endOfSelectedYear)
            ->get(['date_acquired', 'condition', 'quantity', 'total_cost']);

        foreach ($itemsForChart as $item) {
            if (!$item->date_acquired) continue;

            $acquiredDate = Carbon::parse($item->date_acquired);
            
            // Determine the "Start Month" index (0 = Jan, 11 = Dec)
            // If bought in a previous year, it exists from January (Index 0).
            // If bought in the selected year, it exists starting from its purchase month.
            if ($acquiredDate->year < $selectedYear) {
                $startIndex = 0; // Exists from Jan 1st
            } else {
                $startIndex = $acquiredDate->month - 1; // Exists from purchase month
            }

            $qty = (int) $item->quantity;
            $cost = (float) $item->total_cost;
            $condition = $item->condition;

            // Loop from the Start Month to Dec (Index 11)
            // This "carries over" the stock to subsequent months, creating the history effect
            for ($i = $startIndex; $i < 12; $i++) {
                if (isset($chartData[$condition])) {
                    $chartData[$condition][$i] += $qty;
                }
                $chartData['TotalItems'][$i] += $qty;
                $chartData['TotalValue'][$i] += $cost;
            }
        }

        // ✅ FIX 2: Added 'type', 'total_cost', and 'condition' to the select list
        // 'total_cost' is needed for the new Value column in Item Summary
        // 'condition' is needed for filtering by Serviceable/Unserviceable/BER
        $allItems = (clone $query)->get(['name', 'type', 'quantity', 'total_cost', 'condition']);

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
            'selectedYear',
            'allItems'
        ));
    }
}