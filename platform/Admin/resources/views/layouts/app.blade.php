<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @stack('styles')
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Top Navigation -->
    <nav class="bg-indigo-600 text-white px-6 py-3">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <button id="sidebar-toggle" class="lg:hidden">
                    <i class="fas fa-bars"></i>
                </button>
                <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold">{{ config('admin.dashboard.title', 'Admin Panel') }}</a>
            </div>
            <div class="flex items-center space-x-4">
                <div class="relative group">
                    <button class="flex items-center space-x-1">
                        <span>{{ Auth::guard('admin')->user()->name }}</span>
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden group-hover:block">
                        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-indigo-500 hover:text-white">
                            <i class="fas fa-user-circle mr-2"></i> Profile
                        </a>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-indigo-500 hover:text-white">
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
        <aside id="sidebar" class="bg-indigo-800 text-white w-64 min-h-screen p-4 hidden lg:block">
            <nav>
                <ul class="space-y-2">
                    @foreach(config('admin.menu') as $key => $item)
                        @if(!isset($item['permission']) || Auth::guard('admin')->user()->roles()->whereHas('permissions', function($query) use ($item) { $query->where('name', $item['permission']); })->exists())
                            <li>
                                <a href="{{ route($item['route']) }}" class="flex items-center space-x-2 p-2 rounded-md hover:bg-indigo-700 {{ request()->routeIs($item['route']) ? 'bg-indigo-700' : '' }}">
                                    <i class="fas fa-{{ $item['icon'] ?? 'circle' }} w-5"></i>
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
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('hidden');
        });
    </script>
    @stack('scripts')
</body>
</html>
