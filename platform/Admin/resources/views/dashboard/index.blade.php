@extends('admin::layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-gray-600">Welcome to the admin dashboard</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100 text-indigo-600 mr-4">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Total Users</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['users_count']) }}</p>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-green-500 text-sm">
                    <i class="fas fa-arrow-up mr-1"></i>
                    {{ number_format($stats['users_new']) }} new in last 30 days
                </p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-user-check text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Active Users</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['users_active']) }}</p>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-blue-500 text-sm">
                    <i class="fas fa-chart-line mr-1"></i>
                    {{ round(($stats['users_active'] / max($stats['users_count'], 1)) * 100) }}% activity rate
                </p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <i class="fas fa-server text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">System Status</p>
                    <p class="text-2xl font-bold text-gray-800">Healthy</p>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-purple-500 text-sm">
                    <i class="fas fa-check-circle mr-1"></i>
                    All systems operational
                </p>
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Recent Users</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-2 px-4 text-left text-gray-600">Name</th>
                        <th class="py-2 px-4 text-left text-gray-600">Email</th>
                        <th class="py-2 px-4 text-left text-gray-600">Joined</th>
                        <th class="py-2 px-4 text-left text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($recentUsers as $user)
                        <tr>
                            <td class="py-2 px-4">{{ $user->name }}</td>
                            <td class="py-2 px-4">{{ $user->email }}</td>
                            <td class="py-2 px-4">{{ $user->created_at->diffForHumans() }}</td>
                            <td class="py-2 px-4">
                                <a href="{{ route('admin.users.show', $user) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4 text-right">
            <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-900">
                View all users <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Quick Links</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.users.create') }}" class="flex items-center p-3 bg-gray-50 rounded-md hover:bg-indigo-50 transition-colors">
                <div class="p-2 rounded-full bg-indigo-100 text-indigo-600 mr-3">
                    <i class="fas fa-user-plus"></i>
                </div>
                <span>Add New User</span>
            </a>
            <a href="{{ route('admin.roles.index') }}" class="flex items-center p-3 bg-gray-50 rounded-md hover:bg-indigo-50 transition-colors">
                <div class="p-2 rounded-full bg-indigo-100 text-indigo-600 mr-3">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <span>Manage Roles</span>
            </a>
            <a href="{{ route('admin.settings.index') }}" class="flex items-center p-3 bg-gray-50 rounded-md hover:bg-indigo-50 transition-colors">
                <div class="p-2 rounded-full bg-indigo-100 text-indigo-600 mr-3">
                    <i class="fas fa-cog"></i>
                </div>
                <span>System Settings</span>
            </a>
        </div>
    </div>
@endsection
