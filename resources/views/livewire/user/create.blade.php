<form wire:submit.prevent="save">
    <flux:card>
        <flux:card.header>
            <flux:card.title>
                {{ __('Create New User') }}
            </flux:card.title>
            <flux:card.description>
                {{ __('Enter the details for the new user.') }}
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
                <flux:label for="password">{{ __('Password') }}</flux:label>
                <flux:input id="password" type="password" wire:model.defer="password" required />
                <flux:error :field="'password'" />
            </div>

            {{-- Password Confirmation Field --}}
            <div>
                <flux:label for="password_confirmation">{{ __('Confirm Password') }}</flux:label>
                <flux:input id="password_confirmation" type="password" wire:model.defer="password_confirmation" required />
                {{-- No error display needed here, 'confirmed' rule handles it --}}
            </div>
        </flux:card.content>
        <flux:card.footer class="flex justify-end space-x-2">
            <flux:button variant="outline" type="button" wire:click="$dispatch('closeModal')">
                {{ __('Cancel') }}
            </flux:button>
            <flux:button type="submit" wire:loading.attr="disabled">
                {{ __('Save User') }}
            </flux:button>
        </flux:card.footer>
    </flux:card>
</form>
