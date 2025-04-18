@extends('admin::layouts.app')

@section('title', 'Configuration Management')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Configuration Management</h1>
            <p class="text-gray-600">Manage system configurations</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.config.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-plus mr-2"></i> Add New Configuration
            </a>
            <form action="{{ route('admin.config.sync-from-env') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-colors">
                    <i class="fas fa-download mr-2"></i> Sync from .env
                </button>
            </form>
            <form action="{{ route('admin.config.sync-to-env') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors">
                    <i class="fas fa-upload mr-2"></i> Sync to .env
                </button>
            </form>
            <button id="importBtn" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-file-import mr-2"></i> Import
            </button>
        </div>
    </div>

        <!-- Group Tabs -->
        <div class="mb-6 border-b border-gray-200">
            <ul class="flex flex-wrap -mb-px">
                <li class="mr-2">
                    <a href="{{ route('admin.config.index') }}"
                       class="inline-block p-4 {{ !$group ? 'border-b-2 border-indigo-500 text-indigo-600' : 'border-b-2 border-transparent hover:border-gray-300 text-gray-500 hover:text-gray-600' }}">
                        All
                    </a>
                </li>
                @foreach($groups as $groupName)
                    <li class="mr-2">
                        <a href="{{ route('admin.config.index', ['group' => $groupName]) }}"
                           class="inline-block p-4 {{ $group === $groupName ? 'border-b-2 border-indigo-500 text-indigo-600' : 'border-b-2 border-transparent hover:border-gray-300 text-gray-500 hover:text-gray-600' }}">
                            {{ ucfirst($groupName) }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Error Message -->
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Configurations Table -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                            <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Key</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Group</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($configurations as $config)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $config->key }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($config->type === 'boolean')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $config->value ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $config->value ? 'true' : 'false' }}
                                    </span>
                                @elseif($config->type === 'array' || $config->type === 'json')
                                    <span class="text-xs font-mono bg-gray-100 p-1 rounded">
                                        {{ is_array($config->value) ? json_encode($config->value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $config->value }}
                                    </span>
                                @else
                                    {{ Str::limit((string) $config->value, 50) }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($config->type) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ $config->group }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ Str::limit($config->description, 50) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                <a href="{{ route('admin.config.edit', $config) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form action="{{ route('admin.config.destroy', $config) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this configuration?');">
                                    @csrf
                                    @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                    </button>
                                </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                No configurations found for this group.
                            </td>
                        </tr>
                    @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(method_exists($configurations, 'links'))
                <div class="mt-4">
                    {{ $configurations->appends(['group' => $group])->links() }}
                </div>
                @endif

        <!-- Import Modal -->
        <div id="importModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center" style="display: none;">
            <div class="bg-white rounded-lg p-8 max-w-md w-full">
                <h2 class="text-xl font-semibold mb-4">Import Configurations</h2>
                <form action="{{ route('admin.config.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="import_file" class="block text-sm font-medium text-gray-700 mb-2">JSON File</label>
                        <input type="file" name="import_file" id="import_file" accept=".json" class="w-full p-2 border border-gray-300 rounded">
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" id="cancelImport" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Cancel</button>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Import</button>
                    </div>
                </form>
            </div>
        </div>

    <script>
        // Import Modal
        const importBtn = document.getElementById('importBtn');
        const importModal = document.getElementById('importModal');
        const cancelImport = document.getElementById('cancelImport');

        importBtn.addEventListener('click', () => {
            importModal.classList.remove('hidden');
            importModal.style.display = 'flex';
        });

        cancelImport.addEventListener('click', () => {
            importModal.classList.add('hidden');
            importModal.style.display = 'none';
        });
    </script>
@endsection
