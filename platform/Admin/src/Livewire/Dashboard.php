<?php

namespace Platform\Admin\Livewire;

use Livewire\Component;
use Platform\Admin\Models\Admin;
use Platform\Admin\Models\Role;
use Platform\Admin\Models\Permission;

class Dashboard extends Component
{
    public $stats = [];
    
    public function mount()
    {
        $this->stats = [
            'admins' => Admin::count(),
            'roles' => Role::count(),
            'permissions' => Permission::count(),
            'recent_admins' => Admin::latest()->take(5)->get(),
        ];
    }
    
    public function render()
    {
        return view('admin::livewire.dashboard', [
            'stats' => $this->stats
        ]);
    }
}
