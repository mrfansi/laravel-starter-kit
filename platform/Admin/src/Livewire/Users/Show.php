<?php

namespace Platform\Admin\Livewire\Users;

use Livewire\Component;
use Platform\Admin\Models\Admin;

class Show extends Component
{
    public Admin $user;
    
    public function mount(Admin $user)
    {
        $this->user = $user;
    }
    
    public function render()
    {
        return view('admin::livewire.users.show');
    }
}
