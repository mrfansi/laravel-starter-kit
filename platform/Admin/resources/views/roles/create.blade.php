@extends('admin::layouts.app')

@section('title', 'Create Role')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Create Role</h1>
        <p class="text-gray-600">Add a new role</p>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <form action="{{ route('admin.roles.store') }}" method="POST" class="p-6">
            @csrf

            @if($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Validation Error</p>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-bold mb-2">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <p class="text-sm text-gray-500 mt-1">Unique identifier (e.g., admin, editor)</p>
            </div>

            <div class="mb-4">
                <label for="display_name" class="block text-gray-700 font-bold mb-2">Display Name</label>
                <input type="text" name="display_name" id="display_name" value="{{ old('display_name') }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <p class="text-sm text-gray-500 mt-1">Human-readable name (e.g., Administrator, Editor)</p>
            </div>

            <div class="mb-6">
                <label for="description" class="block text-gray-700 font-bold mb-2">Description</label>
                <textarea name="description" id="description" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('admin.roles.index') }}" class="text-indigo-600 hover:text-indigo-900">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Roles
                </a>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <i class="fas fa-save mr-1"></i> Create Role
                </button>
            </div>
        </form>
    </div>
@endsection
