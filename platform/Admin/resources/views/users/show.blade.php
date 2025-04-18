@extends('admin::layouts.app')

@section('title', 'User Details')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">User Details</h1>
        <p class="text-gray-600">View user information</p>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ $user->name }}</h2>
                    <p class="text-gray-600">{{ $user->email }}</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                            <i class="fas fa-trash mr-1"></i> Delete
                        </button>
                    </form>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">ID</dt>
                        <dd class="mt-1 text-gray-900">{{ $user->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                        <dd class="mt-1 text-gray-900">{{ $user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-gray-900">{{ $user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email Verified</dt>
                        <dd class="mt-1 text-gray-900">
                            @if($user->email_verified_at)
                                <span class="text-green-600">
                                    <i class="fas fa-check-circle mr-1"></i> 
                                    {{ $user->email_verified_at->format('M d, Y H:i') }}
                                </span>
                            @else
                                <span class="text-red-600">
                                    <i class="fas fa-times-circle mr-1"></i> Not verified
                                </span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                        <dd class="mt-1 text-gray-900">{{ $user->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                        <dd class="mt-1 text-gray-900">{{ $user->updated_at->format('M d, Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="mt-6 flex justify-between">
                <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-900">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Users
                </a>
            </div>
        </div>
    </div>
@endsection
