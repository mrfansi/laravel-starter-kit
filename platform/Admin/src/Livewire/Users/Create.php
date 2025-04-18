<?php

namespace Platform\Admin\Livewire\Users;

use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Platform\Admin\Models\Admin;
use Platform\Admin\Models\Role;

class Create extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $selectedRoles = [];
    
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8|confirmed',
            'selectedRoles' => 'array',
        ];
    }
    
    public function createUser()
    {
        $this->validate();
        
        $user = Admin::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'email_verified_at' => now(),
        ]);
        
        if (!empty($this->selectedRoles)) {
            $user->roles()->attach($this->selectedRoles);
        }
        
        session()->flash('success', 'User created successfully.');
        return $this->redirect(route('admin.users.index'), navigate: true);
    }
    
    public function render()
    {
        $roles = Role::all();
        
        return view('admin::livewire.users.create', [
            'roles' => $roles,
        ]);
    }
}
