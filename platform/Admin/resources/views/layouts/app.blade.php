<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Custom styles */
        .btn-primary {
            @apply bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md transition-colors;
        }
        .btn-secondary {
            @apply bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition-colors;
        }
        .btn-success {
            @apply bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-colors;
        }
        .btn-danger {
            @apply bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition-colors;
        }
        .btn-warning {
            @apply bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md transition-colors;
        }
        .btn-info {
            @apply bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors;
        }
        .card {
            @apply bg-white shadow-md rounded-lg overflow-hidden;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Top Navigation -->
    <nav class="bg-indigo-600 text-white px-6 py-4 shadow-md">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <button id="sidebar-toggle" class="lg:hidden focus:outline-none focus:ring-2 focus:ring-white rounded">
                    <i class="fas fa-bars"></i>
                </button>
                <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold">{{ config('admin.dashboard.title', 'Admin Panel') }}</a>
            </div>
            <div class="flex items-center space-x-4">
                <div class="relative group">
                    <button class="flex items-center space-x-2 focus:outline-none">
                        <span class="hidden sm:inline">{{ Auth::guard('admin')->user()->name }}</span>
                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-indigo-500">
                            <span class="text-sm font-medium leading-none text-white">{{ substr(Auth::guard('admin')->user()->name, 0, 1) }}</span>
                        </span>
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden group-hover:block">
                        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-indigo-500 hover:text-white transition-colors">
                            <i class="fas fa-user-circle mr-2"></i> Profile
                        </a>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-indigo-500 hover:text-white transition-colors">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex flex-1">
        <!-- Sidebar Navigation -->
        <aside id="sidebar" class="bg-indigo-800 text-white w-64 min-h-screen p-4 hidden lg:block shadow-lg">
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-indigo-200 mb-2">Main Navigation</h2>
            </div>
            <nav>
                <ul class="space-y-2">
                    @foreach(config('admin.menu') as $key => $item)
                        @if(!isset($item['permission']) || Auth::guard('admin')->user()->roles()->whereHas('permissions', function($query) use ($item) { $query->where('name', $item['permission']); })->exists())
                            <li>
                                <a href="{{ route($item['route']) }}" class="flex items-center space-x-3 p-3 rounded-md hover:bg-indigo-700 transition-colors {{ request()->routeIs($item['route']) ? 'bg-indigo-700' : '' }}">
                                    <i class="fas fa-{{ $item['icon'] ?? 'circle' }} w-5 text-indigo-300"></i>
                                    <span>{{ $item['title'] }}</span>
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="container mx-auto">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('hidden');
        });

        // Close alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('[role="alert"]');
                alerts.forEach(function(alert) {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 1s';
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 1000);
                });
            }, 5000);
        });
    </script>
    @stack('scripts')
</body>
</html>
