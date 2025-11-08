<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get dashboard statistics
        $stats = [
            'total_registrations' => \App\Models\Registration::count(),
            'pending_kyc' => \App\Models\Registration::where('kyc_status', 'pending')->count(),
            'approved_kyc' => \App\Models\Registration::where('kyc_status', 'approved')->count(),
            'pending_ipv' => \App\Models\UserCaptureVideo::where('status', 'pending')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
