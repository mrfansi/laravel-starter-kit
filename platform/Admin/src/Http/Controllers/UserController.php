<?php

namespace Platform\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Platform\Admin\Models\Admin;
use Platform\Admin\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        
        $users = Admin::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->orderBy($sortField, $sortDirection)
            ->paginate(10);
            
        return view('admin::users.index', compact('users', 'search', 'sortField', 'sortDirection'));
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin::users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'array',
        ]);
        
        $user = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);
        
        if ($request->has('roles')) {
            $user->roles()->attach($request->roles);
        }
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     *
     * @param  \Platform\Admin\Models\Admin  $user
     * @return \Illuminate\View\View
     */
    public function show(Admin $user)
    {
        return view('admin::users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  \Platform\Admin\Models\Admin  $user
     * @return \Illuminate\View\View
     */
    public function edit(Admin $user)
    {
        $roles = Role::all();
        $selectedRoles = $user->roles->pluck('id')->toArray();
        return view('admin::users.edit', compact('user', 'roles', 'selectedRoles'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Platform\Admin\Models\Admin  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Admin $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'array',
        ]);
        
        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        
        $user->update($data);
        
        $user->roles()->sync($request->roles ?? []);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  \Platform\Admin\Models\Admin  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Admin $user)
    {
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
