<form wire:submit.prevent="update">
    <flux:card>
        <flux:card.header>
            <flux:card.title>
                {{ __('Edit User') }}: {{ $user->name }}
            </flux:card.title>
            <flux:card.description>
                {{ __('Update the details for this user.') }}
            </flux:card.description>
        </flux:card.header>
        <flux:card.content class="space-y-4">
            {{-- Name Field --}}
            <div>
                <flux:label for="name">{{ __('Name') }}</flux:label>
                <flux:input id="name" type="text" wire:model.defer="name" required />
                <flux:error :field="'name'" />
            </div>

            {{-- Email Field --}}
            <div>
                <flux:label for="email">{{ __('Email') }}</flux:label>
                <flux:input id="email" type="email" wire:model.defer="email" required />
                <flux:error :field="'email'" />
            </div>

            {{-- Password Field --}}
            <div>
                <flux:label for="password">{{ __('New Password (optional)') }}</flux:label>
                <flux:input id="password" type="password" wire:model.defer="password" placeholder="Leave blank to keep current password" />
                <flux:error :field="'password'" />
            </div>

            {{-- Password Confirmation Field --}}
            <div>
                <flux:label for="password_confirmation">{{ __('Confirm New Password') }}</flux:label>
                <flux:input id="password_confirmation" type="password" wire:model.defer="password_confirmation" />
                 {{-- No error display needed here, 'confirmed' rule handles it --}}
            </div>
        </flux:card.content>
        <flux:card.footer class="flex justify-end space-x-2">
            <flux:button variant="outline" type="button" wire:click="$dispatch('closeModal')">
                {{ __('Cancel') }}
            </flux:button>
            <flux:button type="submit" wire:loading.attr="disabled">
                {{ __('Update User') }}
            </flux:button>
        </flux:card.footer>
    </flux:card>
</form>
