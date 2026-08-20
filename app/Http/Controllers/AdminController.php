<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Services\OdooPgService;

class AdminController extends Controller
{
    protected OdooPgService $odooPgService;

    public function __construct(OdooPgService $odooPgService)
    {
        $this->odooPgService = $odooPgService;
    }

    /**
     * Show the admin dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $stats = $this->odooPgService->getDashboardStats();

        return view('admin.dashboard', compact('user', 'stats'));
    }
}
