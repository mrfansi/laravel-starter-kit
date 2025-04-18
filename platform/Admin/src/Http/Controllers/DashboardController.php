<?php

namespace Platform\Admin\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Platform\User\Models\User;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        $stats = [
            'users_count' => User::count(),
            'users_new' => User::where('created_at', '>=', now()->subDays(30))->count(),
            'users_active' => User::where('updated_at', '>=', now()->subDays(30))->count(),
        ];
        
        $recentUsers = User::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        return view('admin::dashboard.index', compact('stats', 'recentUsers'));
    }
}
