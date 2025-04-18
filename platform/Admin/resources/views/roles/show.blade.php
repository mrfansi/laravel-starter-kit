@extends('admin::layouts.app')

@section('title', 'Role Details')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Role Details</h1>
        <p class="text-gray-600">View role information and permissions</p>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ $role->display_name }}</h2>
                    <p class="text-gray-600">{{ $role->name }}</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.roles.edit', $role) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    <a href="{{ route('admin.roles.permissions', $role) }}" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                        <i class="fas fa-key mr-1"></i> Manage Permissions
                    </a>
                    @if($role->name !== 'super-admin')
                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this role?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                                <i class="fas fa-trash mr-1"></i> Delete
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">ID</dt>
                        <dd class="mt-1 text-gray-900">{{ $role->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                        <dd class="mt-1 text-gray-900">{{ $role->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Display Name</dt>
                        <dd class="mt-1 text-gray-900">{{ $role->display_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-gray-900">{{ $role->description ?: 'No description' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                        <dd class="mt-1 text-gray-900">{{ $role->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                        <dd class="mt-1 text-gray-900">{{ $role->updated_at->format('M d, Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Permissions ({{ $role->permissions->count() }})</h3>
                
                @if($role->permissions->isEmpty())
                    <p class="text-gray-500">No permissions assigned to this role.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($role->permissions->groupBy('group') as $group => $permissions)
                            <div class="bg-gray-50 p-4 rounded-md">
                                <h4 class="font-medium text-gray-900 mb-2 capitalize">{{ $group }}</h4>
                                <ul class="space-y-1">
                                    @foreach($permissions as $permission)
                                        <li class="text-gray-700">
                                            <i class="fas fa-check text-green-500 mr-1"></i>
                                            {{ $permission->display_name }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="mt-6 flex justify-between">
                <a href="{{ route('admin.roles.index') }}" class="text-indigo-600 hover:text-indigo-900">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Roles
                </a>
            </div>
        </div>
    </div>
@endsection
