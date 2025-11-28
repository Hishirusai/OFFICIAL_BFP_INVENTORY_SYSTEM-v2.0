<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ReportsController extends Controller
{
    public function index()
    {
        // Fetch logs, newest first, with user data
        $logs = ActivityLog::with('user')->latest()->paginate(12);
        
        return view('reports.index', compact('logs'));
    }
}