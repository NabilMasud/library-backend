<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('causer', 'subject')->latest();

        // Filter berdasarkan log_name
        if ($request->log_name) {
            $query->where('log_name', 'like', '%' . $request->log_name . '%');
        }

        // Filter berdasarkan causer
        if ($request->causer) {
            $query->whereHas('causer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->causer . '%');
            });
        }

        // Filter berdasarkan rentang tanggal
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $logs = $query->paginate(5);

        return Inertia::render('Admin/ActivityLogs', [
            'logs' => $logs->items(),
            'current_page' => $logs->currentPage(),
            'total' => $logs->total(),
            'per_page' => $logs->perPage(),
        ]);
    }
}
