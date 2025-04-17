<?php

namespace App\Livewire\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')] // Add Layout attribute
// Title will be set dynamically in mount or render
class Edit extends Component // Change back to standard Component
{
    public User $user; // Route model binding

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Mount the component and populate the form fields.
     * User model is injected via route model binding.
     */
    public function mount(User $user): void
    {
        $this->user = $user; // Assign the injected user
        $this->name = $this->user->name;
        $this->email = $this->user->email;
    }

    // Add a dynamic title attribute method
    public function title(): string
    {
        return __('Edit User').': '.$this->user->name;
    }

    /**
     * Define validation rules.
     *
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user->id)],
            'password' => ['nullable', 'string', Password::defaults(), 'confirmed'], // Password is optional
        ];
    }

    /**
     * Update the user's information and close modal.
     */
    public function update(): void
    {
        $validated = $this->validate();

        // Prepare data for update
        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        // Only update password if it's provided
        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $this->user->update($updateData);

        session()->flash('message', 'User successfully updated.');

        // Redirect back to index page
        $this->redirect(route('users.index'), navigate: true);
    }

    public function render()
    {
        // Render the view within the specified layout
        return view('livewire.user.edit');
    }
}
