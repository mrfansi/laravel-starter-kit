<div>
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ __('User Management') }}</h1>
        {{-- Link styled as Button --}}
        <flux:button href="{{ route('users.create') }}" wire:navigate>
            {{ __('Create User') }}
        </flux:button>
    </div>

    <flux:card>
        <flux:card.content>
            <flux:table :rows="$users">
                <flux:table.head>
                    <flux:table.header>{{ __('ID') }}</flux:table.header>
                    <flux:table.header>{{ __('Name') }}</flux:table.header>
                    <flux:table.header>{{ __('Email') }}</flux:table.header>
                    <flux:table.header>{{ __('Created At') }}</flux:table.header>
                    <flux:table.header class="text-right">{{ __('Actions') }}</flux:table.header>
                </flux:table.head>
                <flux:table.body>
                    <template x-for="row in $wire.users.data" :key="row.id">
                        <flux:table.row wire:key="user-{{ $user->id }}">
                            <flux:table.cell class="font-medium" x-text="row.id"></flux:table.cell>
                            <flux:table.cell x-text="row.name"></flux:table.cell>
                            <flux:table.cell x-text="row.email"></flux:table.cell>
                            <flux:table.cell x-text="new Date(row.created_at).toLocaleString()"></flux:table.cell>
                            <flux:table.cell class="text-right space-x-2">
                                <flux:button variant="outline" size="sm" :href="`/users/${row.id}/edit`"
                                    wire:navigate>
                                    {{ __('Edit') }}
                                </flux:button>
                                <flux:dialog>
                                    <flux:dialog.trigger>
                                        <flux:button variant="destructive" size="sm"
                                            wire:click="confirmDelete(row.id)" wire:loading.attr="disabled">
                                            {{ __('Delete') }}
                                        </flux:button>
                                    </flux:dialog.trigger>
                                    <flux:dialog.content>
                                        <flux:dialog.header>
                                            <flux:dialog.title>{{ __('Delete User') }}</flux:dialog.title>
                                            <flux:dialog.description>
                                                {{ __('Are you sure you want to delete this user? This action cannot be undone.') }}
                                            </flux:dialog.description>
                                        </flux:dialog.header>
                                        <flux:dialog.footer class="sm:justify-start">
                                            <flux:button variant="destructive" wire:click="deleteUser()"
                                                wire:loading.attr="disabled">
                                                {{ __('Delete') }}
                                            </flux:button>
                                            <flux:dialog.close>
                                                <flux:button variant="outline">
                                                    {{ __('Cancel') }}
                                                </flux:button>
                                            </flux:dialog.close>
                                        </flux:dialog.footer>
                                    </flux:dialog.content>
                                </flux:dialog>
                            </flux:table.cell>
                        </flux:table.row>
                    </template>
                </flux:table.body>
                <flux:table.empty>
                    {{ __('No users found.') }}
                </flux:table.empty>
            </flux:table>
        </flux:card.content>
        @if ($users->hasPages())
            <flux:card.footer>
                {{ $users->links() }} {{-- Ensure pagination view is styled for Tailwind/Flux --}}
            </flux:card.footer>
        @endif
    </flux:card>
</div>
