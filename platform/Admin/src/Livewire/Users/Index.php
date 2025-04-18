<?php

namespace Platform\Admin\Livewire\Users;

use Livewire\Component;
use Platform\Admin\Models\Admin;

class Index extends Component
{
    public $search = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];
    
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }
    
    public function deleteUser($userId)
    {
        $user = Admin::find($userId);
        if ($user) {
            $user->delete();
            session()->flash('success', 'User deleted successfully.');
        }
    }
    
    public function render()
    {
        $users = Admin::query()
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
            
        return view('admin::livewire.users.index', [
            'users' => $users,
        ]);
    }
}
