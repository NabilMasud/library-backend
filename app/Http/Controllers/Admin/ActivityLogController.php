<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use Inertia\Inertia;

class ActivityLogController extends Controller
{
    public function index()
    {
        // Ambil data log aktivitas dari database
        $logs = Activity::with('causer', 'subject')->latest()->paginate(25);

        return Inertia::render('Admin/ActivityLogs', [
            'logs' => $logs->items(),          // Data per halaman
            'current_page' => $logs->currentPage(),  // Halaman sekarang
            'total' => $logs->total(),          // Total semua data
            'per_page' => $logs->perPage(),     // Banyak data per halaman
        ]);
    }
}
