@extends('admin::layouts.app')

@section('title', 'Configuration Management')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold">Configuration Management</h1>
            <div class="flex space-x-2">
                <a href="{{ route('config.configurations.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    Add New Configuration
                </a>
                <button id="importBtn" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                    Import
                </button>
                <a href="{{ route('config.configurations.export') }}" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded">
                    Export
                </a>
            </div>
        </div>

        <!-- Group Tabs -->
        <div class="mb-6 border-b border-gray-200">
            <ul class="flex flex-wrap -mb-px">
                @foreach($groups as $groupKey => $groupName)
                    <li class="mr-2">
                        <a href="{{ route('config.configurations.index', ['group' => $groupKey]) }}" 
                           class="inline-block p-4 {{ $group === $groupKey ? 'border-b-2 border-blue-500 text-blue-600' : 'border-b-2 border-transparent hover:border-gray-300 text-gray-500 hover:text-gray-600' }}">
                            {{ $groupName }}
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
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Public</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">System</th>
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
                                        {{ $config->value ? 'True' : 'False' }}
                                    </span>
                                @elseif(in_array($config->type, ['array', 'json']))
                                    <code class="text-xs bg-gray-100 p-1 rounded">
                                        {{ json_encode($config->value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}
                                    </code>
                                @else
                                    {{ Str::limit((string) $config->value, 50) }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($config->type) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ Str::limit($config->description, 50) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $config->is_public ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $config->is_public ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $config->is_system ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $config->is_system ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('config.configurations.edit', $config) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    <form action="{{ route('config.configurations.destroy', $config) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this configuration?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
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
        <div class="mt-4">
            {{ $configurations->appends(['group' => $group])->links() }}
        </div>

        <!-- Import Modal -->
        <div id="importModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center" style="display: none;">
            <div class="bg-white rounded-lg p-8 max-w-md w-full">
                <h2 class="text-xl font-semibold mb-4">Import Configurations</h2>
                <form action="{{ route('config.configurations.import') }}" method="POST" enctype="multipart/form-data">
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
