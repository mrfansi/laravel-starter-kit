@extends('admin::layouts.app')

@section('title', 'Manage Role Permissions')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Manage Permissions</h1>
        <p class="text-gray-600">Assign permissions to the {{ $role->display_name }} role</p>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <form action="{{ route('admin.roles.permissions.update', $role) }}" method="POST" class="p-6">
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

            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Available Permissions</h3>
                    <div>
                        <button type="button" id="select-all" class="text-sm text-indigo-600 hover:text-indigo-900">
                            Select All
                        </button>
                        <span class="text-gray-400 mx-2">|</span>
                        <button type="button" id="deselect-all" class="text-sm text-indigo-600 hover:text-indigo-900">
                            Deselect All
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($permissions as $group => $groupPermissions)
                        <div class="bg-gray-50 p-4 rounded-md">
                            <h4 class="font-medium text-gray-900 mb-3 capitalize">{{ $group }}</h4>
                            <div class="space-y-2">
                                @foreach($groupPermissions as $permission)
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission-{{ $permission->id }}"
                                                   class="permission-checkbox h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                                   {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="permission-{{ $permission->id }}" class="font-medium text-gray-700">{{ $permission->display_name }}</label>
                                            @if($permission->description)
                                                <p class="text-gray-500">{{ $permission->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('admin.roles.show', $role) }}" class="text-indigo-600 hover:text-indigo-900">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Role
                </a>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <i class="fas fa-save mr-1"></i> Save Permissions
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllBtn = document.getElementById('select-all');
        const deselectAllBtn = document.getElementById('deselect-all');
        const checkboxes = document.querySelectorAll('.permission-checkbox');
        
        selectAllBtn.addEventListener('click', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
        });
        
        deselectAllBtn.addEventListener('click', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        });
    });
</script>
@endpush
