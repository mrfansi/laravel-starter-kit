@extends('admin::layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1 class="text-2xl font-semibold mb-6">Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Admins Stats -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300 mr-4">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Total Admins</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['admins'] }}</p>
                </div>
            </div>
        </div>

        <!-- Roles Stats -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300 mr-4">
                    <i class="fas fa-shield-alt text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Total Roles</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['roles'] }}</p>
                </div>
            </div>
        </div>

        <!-- Permissions Stats -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-300 mr-4">
                    <i class="fas fa-key text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Total Permissions</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['permissions'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Admins -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Recent Admin Users</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($stats['recent_admins'] as $admin)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $admin->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $admin->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $admin->created_at->diffForHumans() }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
