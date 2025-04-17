<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('User Management')]
class Index extends Component
{
    use WithPagination;

    public bool $confirmingUserDeletion = false;

    public ?int $userIdToDelete = null;

    public function confirmDelete(int $userId): void
    {
        $this->userIdToDelete = $userId;
        $this->confirmingUserDeletion = true;
    }

    public function deleteUser(): void
    {
        if ($this->userIdToDelete) {
            User::findOrFail($this->userIdToDelete)->delete();
            session()->flash('message', 'User successfully deleted.');
        }

        $this->confirmingUserDeletion = false;
        $this->userIdToDelete = null;
        // Reset pagination to first page after deletion if needed
        // $this->resetPage();
    }

    public function render()
    {
        $users = User::latest()->paginate(10); // Fetch users, 10 per page

        return view('livewire.user.index', [
            'users' => $users,
        ]);
    }
}
