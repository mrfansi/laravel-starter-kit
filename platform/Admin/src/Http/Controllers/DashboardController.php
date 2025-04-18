<?php

namespace Platform\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Platform\Admin\Models\Admin;
use Platform\Admin\Models\Role;
use Platform\Admin\Models\Permission;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $stats = [
            'admins' => Admin::count(),
            'roles' => Role::count(),
            'permissions' => Permission::count(),
            'recent_admins' => Admin::latest()->take(5)->get(),
        ];
        
        return view('admin::dashboard.index', compact('stats'));
    }
}
