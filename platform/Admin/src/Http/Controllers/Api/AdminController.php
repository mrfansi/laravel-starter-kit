<?php

namespace Platform\Admin\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Platform\Admin\Models\Admin;

class AdminController extends Controller
{
    /**
     * Display a listing of the resources.
     */
    public function index()
    {
        $admins = Admin::all();
        
        return response()->json(['data' => $admins]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Add validation rules here
        ]);

        $admin = Admin::create($validated);

        return response()->json(['data' => $admin, 'message' => 'Admin created successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        return response()->json(['data' => $admin]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Admin $admin)
    {
        $validated = $request->validate([
            // Add validation rules here
        ]);

        $admin->update($validated);

        return response()->json(['data' => $admin, 'message' => 'Admin updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        $admin->delete();

        return response()->json(['message' => 'Admin deleted successfully']);
    }
}
