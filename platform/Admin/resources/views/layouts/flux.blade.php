<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Admin Panel' }} - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- Livewire Styles -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/livewire@3.0.0/dist/livewire.min.js" defer></script>

    @stack('styles')
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar -->
            <div id="sidebar" class="bg-indigo-800 text-white w-64 min-h-screen p-4 hidden lg:block">
                <div class="flex items-center justify-center mb-8">
                    <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold">{{ config('admin.dashboard.title') ?? 'Admin Panel' }}</a>
                </div>
                <nav>
                    <ul class="space-y-2">
                        @if(config('admin.menu'))
                            @foreach(config('admin.menu') as $key => $item)
                                @if(!isset($item['permission']) || Auth::guard('admin')->user()->roles()->whereHas('permissions', function($query) use ($item) { $query->where('name', $item['permission']); })->exists())
                                    <li>
                                        @if(isset($item['route']) && Route::has($item['route']))
                                            <a href="{{ route($item['route']) }}" class="flex items-center space-x-2 p-2 rounded-md hover:bg-indigo-700 {{ request()->routeIs($item['route']) ? 'bg-indigo-700' : '' }}">
                                                <i class="fas fa-{{ $item['icon'] ?? 'circle' }} w-5"></i>
                                                <span>{{ $item['title'] }}</span>
                                            </a>
                                        @else
                                            <div class="flex items-center space-x-2 p-2 rounded-md text-gray-400">
                                                <i class="fas fa-{{ $item['icon'] ?? 'circle' }} w-5"></i>
                                                <span>{{ $item['title'] }}</span>
                                            </div>
                                        @endif
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    </ul>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Top Navigation -->
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="flex justify-between items-center py-4 px-6">
                        <div class="flex items-center space-x-4">
                            <button id="sidebar-toggle" class="lg:hidden text-gray-500 hover:text-gray-700 focus:outline-none">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                                <button @click="open = !open" class="flex items-center space-x-1 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white focus:outline-none">
                                    <span>{{ Auth::guard('admin')->user()->name }}</span>
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="open" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 z-10">
                                    <a href="#" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-indigo-500 hover:text-white">
                                        <i class="fas fa-user-circle mr-2"></i> Profile
                                    </a>
                                    <form method="POST" action="{{ route('admin.logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-indigo-500 hover:text-white">
                                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900 p-6">
                    @if(session('success'))
                        <div class="bg-green-100 dark:bg-green-900 border-l-4 border-green-500 text-green-700 dark:text-green-300 p-4 mb-6" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 dark:bg-red-900 border-l-4 border-red-500 text-red-700 dark:text-red-300 p-4 mb-6" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>
    </div>

    <!-- Custom Scripts -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Your initialization code here
        });
    </script>

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
