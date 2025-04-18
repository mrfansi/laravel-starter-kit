@extends('admin::layouts.app')

@section('title', 'System Settings')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">System Settings</h1>
        <p class="text-gray-600">Configure application settings</p>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <form action="{{ route('admin.settings.update') }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

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

            <!-- General Settings -->
            <div class="mb-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4">General Settings</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="site_name" class="block text-gray-700 font-medium mb-2">Site Name</label>
                        <input type="text" name="site_name" id="site_name" value="{{ $settings['site_name'] ?? config('app.name') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label for="contact_email" class="block text-gray-700 font-medium mb-2">Contact Email</label>
                        <input type="email" name="contact_email" id="contact_email" value="{{ $settings['contact_email'] ?? '' }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="site_description" class="block text-gray-700 font-medium mb-2">Site Description</label>
                        <textarea name="site_description" id="site_description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $settings['site_description'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>
            
            <!-- Display Settings -->
            <div class="mb-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Display Settings</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="items_per_page" class="block text-gray-700 font-medium mb-2">Items Per Page</label>
                        <input type="number" name="items_per_page" id="items_per_page" value="{{ $settings['items_per_page'] ?? 15 }}" required
                               min="5" max="100"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>
            
            <!-- Feature Settings -->
            <div class="mb-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Feature Settings</h2>
                
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="enable_registration" id="enable_registration" value="1"
                                   {{ isset($settings['enable_registration']) && $settings['enable_registration'] ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="enable_registration" class="font-medium text-gray-700">Enable User Registration</label>
                            <p class="text-gray-500">Allow new users to register on the site</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="enable_social_login" id="enable_social_login" value="1"
                                   {{ isset($settings['enable_social_login']) && $settings['enable_social_login'] ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="enable_social_login" class="font-medium text-gray-700">Enable Social Login</label>
                            <p class="text-gray-500">Allow users to login with social media accounts</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- System Settings -->
            <div class="mb-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4">System Settings</h2>
                
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="maintenance_mode" id="maintenance_mode" value="1"
                                   {{ isset($settings['maintenance_mode']) && $settings['maintenance_mode'] ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="maintenance_mode" class="font-medium text-gray-700">Maintenance Mode</label>
                            <p class="text-gray-500">Put the application into maintenance mode</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <i class="fas fa-save mr-1"></i> Save Settings
                </button>
            </div>
        </form>
    </div>
@endsection
