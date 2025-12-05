<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Station;
use App\Models\Item;
use App\Models\ActivityLog; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

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

    // INDEX FUNCTION
    public function index(Request $request)
    {
        $stationsQuery = Station::query();
        $isSearchingItems = false;
        $searchDisplay = '';

        // Define the query logic for summing columns
        if ($request->filled('strict_code_search')) {
            $code = $request->strict_code_search;
            $isSearchingItems = true;
            $searchDisplay = $request->item_search; 

            $searchLogic = function ($query) use ($code) {
                $query->where('product_code', '=', $code); 
            };
            
            $stationsQuery->whereHas('items', $searchLogic);
            $stationsQuery->withSum(['items as matched_quantity' => $searchLogic], 'quantity');
            $stationsQuery->withSum(['items as matched_cost' => $searchLogic], 'total_cost'); // ✅ Fetch Cost

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
            $stationsQuery->withSum(['items as matched_cost' => $searchLogic], 'total_cost'); // ✅ Fetch Cost
            
        } else {
            // No search: Get totals for EVERYTHING
            $stationsQuery->withSum('items as matched_quantity', 'quantity');
            $stationsQuery->withSum('items as matched_cost', 'total_cost'); // ✅ Fetch Cost
        }

        $stations = $stationsQuery->get();

        // Calculate initial totals for the view
        $totalMatchedQuantity = $stations->sum('matched_quantity');
        $totalMatchedCost = $stations->sum('matched_cost');

        return view('stations.index', compact('stations', 'totalMatchedQuantity', 'totalMatchedCost', 'isSearchingItems', 'searchDisplay'));
    }

    // SHOW FUNCTION
    public function show(Request $request, Station $station)
    {
    // ✅ Sorts alphabetically (A-Z) by name
        $query = $station->items()->orderBy('name', 'asc')->latest();

        // 2. Search Filters (Keep existing logic)
        if ($request->filled('strict_code_search')) {
             $query->where('product_code', '=', $request->strict_code_search);
        } 
        elseif ($request->filled('search') || $request->filled('item_search')) {
            $searchTerm = $request->get('search', $request->get('item_search'));
            $query->where(function($q) use ($searchTerm) {
                $q->where('product_code', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('type', 'LIKE', "%{$searchTerm}%");
            });
        }

        // 3. Other Filters (Keep existing logic)
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

        // ✅ CALCULATE TOTALS FOR CARDS (Before Pagination)
        // We clone the query to get totals of the *filtered* results for this station
        $totalsQuery = clone $query;
        $totalQuantity = $totalsQuery->sum('quantity');
        $totalValue = $totalsQuery->sum('total_cost');

        // 4. PAGINATION (This is for the Table - Keep as is)
        $items = $query->paginate(10)->appends($request->all());

        // ✅ 5. NEW: Fetch ALL items for the Bulk Transfer Dropdown
        // We select specific columns to keep the page load fast.
        $allStationItems = $station->items()
            ->select('id', 'name', 'product_code', 'type', 'quantity', 'unit')
            ->where('quantity', '>', 0) // Optional: Only show items that actually have stock
            ->orderBy('name')
            ->get();

        $itemNames = Item::distinct()->pluck('name');
        $itemTypes = Item::distinct()->pluck('type');

        // ✅ 6. Add 'allStationItems', 'totalQuantity', 'totalValue' to the compact() list
        return view('stations.show', compact('station', 'items', 'itemNames', 'itemTypes', 'allStationItems', 'totalQuantity', 'totalValue'));
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

    // ✅ FIXED: These functions are now INSIDE the class
    public function markNotification(Request $request, $id)
    {
        $notification = \Illuminate\Notifications\DatabaseNotification::find($id);
        
        if($notification && Auth::check()) {
            // Mark as read for the current user only (not globally)
            DB::table('notification_reads')->insertOrIgnore([
                'user_id' => Auth::id(),
                'notification_id' => $notification->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    // CLEAR ALL NOTIFICATIONS FOR A STATION
    public function clearNotifications(Station $station)
    {
        $station->notifications()->delete();
        return back()->with('success', 'Notifications cleared.');
    }

    public function export(Request $request, $id)
    {
        $station = Station::findOrFail($id);

        // --- 1. FILTERING (Same as before) ---
        $query = $station->items()->orderBy('name', 'asc')->latest();

        if ($request->filled('strict_code_search')) {
             $query->where('product_code', '=', $request->strict_code_search);
        } 
        elseif ($request->filled('search') || $request->filled('item_search')) {
            $searchTerm = $request->get('search', $request->get('item_search'));
            $query->where(function($q) use ($searchTerm) {
                $q->where('product_code', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('type', 'LIKE', "%{$searchTerm}%");
            });
        }
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

        $items = $query->get();

        // --- 2. TEMPLATE SETUP ---
        $templatePath = storage_path('app/templates/ics_template.xlsx');
        if (!file_exists($templatePath)) {
            return back()->with('error', 'Template file not found!');
        }

        $spreadsheet = IOFactory::load($templatePath);
        $masterSheet = $spreadsheet->getActiveSheet();
        $masterSheet->setTitle('Page 1');

        // --- 3. PAGINATION SETTINGS ---
        $startRow = 16;
        // Row 16 to 43 = 28 rows total
        $limit    = 28; 

        // Split items into chunks of 28
        $chunks = $items->chunk($limit);

        if ($chunks->isEmpty()) {
            $chunks->push(collect());
        }

        $pageIndex = 0;

        foreach ($chunks as $chunk) {
            $pageIndex++;

            // Use master sheet for Page 1, clone for others
            if ($pageIndex === 1) {
                $sheet = $masterSheet;
            } else {
                $sheet = clone $masterSheet;
                $sheet->setTitle('Page ' . $pageIndex);
                $spreadsheet->addSheet($sheet);
            }

            // --- 4. FILL DATA ---
            $sheet->setCellValue('C11', $station->name);

            $row = $startRow;
            foreach ($chunk as $item) {
                $sheet->setCellValue('A' . $row, $item->quantity);
                $sheet->setCellValue('B' . $row, $item->unit);
                $sheet->setCellValue('C' . $row, $item->unit_cost);
                $sheet->setCellValue('D' . $row, $item->quantity * $item->unit_cost);
                $sheet->setCellValue('E' . $row, $item->name);

                $dateAcquired = $item->created_at ? $item->created_at->format('Y-m-d') : '';
                $sheet->setCellValue('G' . $row, $dateAcquired);

                // Force Text Format for Inventory No.
                $sheet->setCellValueExplicit('H' . $row, $item->product_code, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                
                $sheet->setCellValue('I' . $row, $item->date_expiry);

                // Formatting
                $sheet->getStyle('A' . $row)->getNumberFormat()->setFormatCode('#,##0'); 
                $sheet->getStyle('C' . $row . ':D' . $row)->getNumberFormat()->setFormatCode('#,##0.00'); 
                $sheet->getStyle('A' . $row . ':I' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                $row++;
            }
        }

        // --- 5. DOWNLOAD ---
        $safeStationName = str_replace(['/', '\\'], '-', $station->name);
        $fileName = 'ICS_' . $safeStationName . '_' . now()->setTimezone('Asia/Manila')->format('Y-m-d_H-i-s') . '.xlsx';

        // Reset to first tab so file opens cleanly
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

}