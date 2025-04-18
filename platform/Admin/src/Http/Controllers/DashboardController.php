<?php

namespace Platform\Admin\Http\Controllers;

use Platform\Admin\Models\Admin;
use Platform\Admin\Models\Configuration;
use Platform\Admin\Models\Permission;
use Platform\Admin\Models\Role;

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
            'configurations' => class_exists(Configuration::class) ? Configuration::count() : 0,
            'recent_admins' => Admin::latest()->take(5)->get(),
        ];

        return view('admin::dashboard.index', compact('stats'));
    }
}
