@extends('admin::layouts.app')

@section('title', 'Create Configuration')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold">Create Configuration</h1>
            <a href="{{ route('config.configurations.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                Back to List
            </a>
        </div>

        <!-- Error Messages -->
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Configuration Form -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
            <form action="{{ route('config.configurations.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Key -->
                    <div class="col-span-1">
                        <label for="key" class="block text-sm font-medium text-gray-700 mb-2">Key *</label>
                        <input type="text" name="key" id="key" value="{{ old('key') }}" required
                               class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="app.name">
                        <p class="text-xs text-gray-500 mt-1">Unique identifier for this configuration (e.g., app.name, mail.host)</p>
                    </div>

                    <!-- Group -->
                    <div class="col-span-1">
                        <label for="group" class="block text-sm font-medium text-gray-700 mb-2">Group *</label>
                        <select name="group" id="group" required
                                class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @foreach($groups as $groupKey => $groupName)
                                <option value="{{ $groupKey }}" {{ old('group') === $groupKey ? 'selected' : '' }}>{{ $groupName }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Category for this configuration</p>
                    </div>

                    <!-- Type -->
                    <div class="col-span-1">
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                        <select name="type" id="type" required
                                class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @foreach($types as $typeKey => $typeName)
                                <option value="{{ $typeKey }}" {{ old('type') === $typeKey ? 'selected' : '' }}>{{ $typeName }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Data type of this configuration</p>
                    </div>

                    <!-- Value -->
                    <div class="col-span-1">
                        <label for="value" class="block text-sm font-medium text-gray-700 mb-2">Value</label>
                        <textarea name="value" id="value" rows="3"
                                  class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Configuration value">{{ old('value') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">For boolean, use 'true' or 'false'. For array/JSON, enter valid JSON.</p>
                    </div>

                    <!-- Description -->
                    <div class="col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" id="description" rows="2"
                                  class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Description of this configuration">{{ old('description') }}</textarea>
                    </div>

                    <!-- Flags -->
                    <div class="col-span-2 flex space-x-6">
                        <div class="flex items-center">
                            <input type="checkbox" name="is_system" id="is_system" value="1" {{ old('is_system') ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_system" class="ml-2 block text-sm text-gray-700">System Configuration</label>
                            <span class="ml-1 text-xs text-gray-500">(Used by the system internally)</span>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="is_public" id="is_public" value="1" {{ old('is_public') ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_public" class="ml-2 block text-sm text-gray-700">Public Configuration</label>
                            <span class="ml-1 text-xs text-gray-500">(Accessible via public API)</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                        Create Configuration
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Dynamic form based on type
        const typeSelect = document.getElementById('type');
        const valueField = document.getElementById('value');

        typeSelect.addEventListener('change', function() {
            if (this.value === 'boolean') {
                valueField.placeholder = 'true or false';
            } else if (this.value === 'integer') {
                valueField.placeholder = 'Enter a number';
            } else if (this.value === 'array' || this.value === 'json') {
                valueField.placeholder = '{"key": "value"} or ["item1", "item2"]';
            } else {
                valueField.placeholder = 'Configuration value';
            }
        });

        // Trigger on page load
        typeSelect.dispatchEvent(new Event('change'));
    </script>
@endsection
