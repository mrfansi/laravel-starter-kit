<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Edit User</h1>
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Back to Users
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <form wire:submit="updateUser" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                    <input wire:model="name" type="text" id="name" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-900 dark:text-white" placeholder="Enter name">
                    @error('name') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <input wire:model="email" type="email" id="email" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-900 dark:text-white" placeholder="Enter email">
                    @error('email') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password (leave empty to keep current)</label>
                    <input wire:model="password" type="password" id="password" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-900 dark:text-white" placeholder="Enter new password">
                    @error('password') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm Password</label>
                    <input wire:model="password_confirmation" type="password" id="password_confirmation" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-900 dark:text-white" placeholder="Confirm new password">
                </div>
            </div>

            <!-- Roles -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Roles</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($roles as $role)
                        <label class="inline-flex items-center">
                            <input wire:model="selectedRoles" type="checkbox" value="{{ $role->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-900 dark:border-gray-700">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $role->display_name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('selectedRoles') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-save mr-2"></i> Update User
                </button>
            </div>
        </form>
    </div>
</div>
