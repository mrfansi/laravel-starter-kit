@extends('admin::layouts.app')

@section('title', 'Roles Management')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Roles Management</h1>
            <p class="text-gray-600">Manage roles and permissions</p>
        </div>
        <a href="{{ route('admin.roles.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
            <i class="fas fa-plus mr-2"></i> Add New Role
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 text-left text-gray-600">ID</th>
                            <th class="py-3 px-4 text-left text-gray-600">Name</th>
                            <th class="py-3 px-4 text-left text-gray-600">Display Name</th>
                            <th class="py-3 px-4 text-left text-gray-600">Permissions</th>
                            <th class="py-3 px-4 text-left text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($roles as $role)
                            <tr>
                                <td class="py-3 px-4">{{ $role->id }}</td>
                                <td class="py-3 px-4">{{ $role->name }}</td>
                                <td class="py-3 px-4">{{ $role->display_name }}</td>
                                <td class="py-3 px-4">
                                    <span class="text-sm text-gray-600">{{ $role->permissions->count() }} permissions</span>
                                </td>
                                <td class="py-3 px-4 flex space-x-2">
                                    <a href="{{ route('admin.roles.show', $role) }}" class="text-blue-600 hover:text-blue-900" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.roles.edit', $role) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.roles.permissions', $role) }}" class="text-green-600 hover:text-green-900" title="Manage Permissions">
                                        <i class="fas fa-key"></i>
                                    </a>
                                    @if($role->name !== 'super-admin')
                                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this role?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-3 px-4 text-center text-gray-500">No roles found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $roles->links() }}
            </div>
        </div>
    </div>
@endsection
