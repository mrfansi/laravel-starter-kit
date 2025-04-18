<?php

namespace Platform\Admin\Livewire\Users;

use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Platform\Admin\Models\Admin;
use Platform\Admin\Models\Role;

class Edit extends Component
{
    public Admin $user;
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $selectedRoles = [];
    
    public function mount(Admin $user)
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->selectedRoles = $user->roles->pluck('id')->toArray();
    }
    
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins,email,' . $this->user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'selectedRoles' => 'array',
        ];
    }
    
    public function updateUser()
    {
        $this->validate();
        
        $data = [
            'name' => $this->name,
            'email' => $this->email,
        ];
        
        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }
        
        $this->user->update($data);
        
        // Sync roles
        $this->user->roles()->sync($this->selectedRoles);
        
        session()->flash('success', 'User updated successfully.');
        return $this->redirect(route('admin.users.index'), navigate: true);
    }
    
    public function render()
    {
        $roles = Role::all();
        
        return view('admin::livewire.users.edit', [
            'roles' => $roles,
        ]);
    }
}
