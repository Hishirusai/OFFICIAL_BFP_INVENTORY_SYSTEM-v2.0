<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        // 1. Get unique action types for the dropdown (Dynamic)
        $actionTypes = ActivityLog::distinct()
                        ->pluck('action_type')
                        ->sort();

        // 2. Start the query
        $query = ActivityLog::with('user')->latest();

        // 3. Apply Filter if selected
        if ($request->has('action_type') && $request->action_type != '') {
            $query->where('action_type', $request->action_type);
        }

        // 4. Paginate results
        $logs = $query->paginate(12);
        
        // 5. Pass both $logs and $actionTypes to the view
        return view('reports.index', compact('logs', 'actionTypes'));
    }
}