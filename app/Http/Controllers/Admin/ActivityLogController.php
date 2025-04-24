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
        $logs = Activity::with('causer', 'subject')->latest()->get();

        return Inertia::render('Admin/ActivityLogs', [
            'logs' => $logs,
        ]);
    }
}
