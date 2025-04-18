<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">User Details</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Back to Users
            </a>
            <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition-colors">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- User Information -->
                <div>
                    <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">User Information</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</h3>
                            <p class="mt-1 text-base font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</h3>
                            <p class="mt-1 text-base font-medium text-gray-900 dark:text-white">{{ $user->email }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Created At</h3>
                            <p class="mt-1 text-base font-medium text-gray-900 dark:text-white">{{ $user->created_at->format('F j, Y, g:i a') }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</h3>
                            <p class="mt-1 text-base font-medium text-gray-900 dark:text-white">{{ $user->updated_at->format('F j, Y, g:i a') }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Roles & Permissions -->
                <div>
                    <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Roles & Permissions</h2>
                    
                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Assigned Roles</h3>
                        @if($user->roles->count() > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($user->roles as $role)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                        {{ $role->display_name }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400 italic">No roles assigned</p>
                        @endif
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Permissions (via Roles)</h3>
                        @php
                            $permissions = collect();
                            foreach($user->roles as $role) {
                                $permissions = $permissions->merge($role->permissions);
                            }
                            $permissions = $permissions->unique('id');
                        @endphp
                        
                        @if($permissions->count() > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($permissions as $permission)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        {{ $permission->display_name }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400 italic">No permissions assigned</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
