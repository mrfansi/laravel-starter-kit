<?php

namespace Platform\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $settings = $this->getSettings();
        
        return view('admin::settings.index', compact('settings'));
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string',
            'contact_email' => 'required|email',
            'items_per_page' => 'required|integer|min:5|max:100',
            'enable_registration' => 'boolean',
            'enable_social_login' => 'boolean',
            'maintenance_mode' => 'boolean',
        ]);

        // Convert checkbox values to boolean
        $validated['enable_registration'] = $request->has('enable_registration');
        $validated['enable_social_login'] = $request->has('enable_social_login');
        $validated['maintenance_mode'] = $request->has('maintenance_mode');

        // Save settings
        foreach ($validated as $key => $value) {
            $this->updateSetting($key, $value);
        }

        // Clear settings cache
        Cache::forget('admin_settings');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }

    /**
     * Get all settings.
     */
    protected function getSettings(): array
    {
        return Cache::remember('admin_settings', 60 * 60, function () {
            $settings = [
                'site_name' => 'Laravel Starter Kit',
                'site_description' => 'A comprehensive starter kit for Laravel 12 applications',
                'contact_email' => 'admin@example.com',
                'items_per_page' => 15,
                'enable_registration' => true,
                'enable_social_login' => false,
                'maintenance_mode' => false,
            ];

            // Load settings from database if available
            // This is a placeholder for actual database implementation
            // In a real application, you would load settings from a database table
            
            return $settings;
        });
    }

    /**
     * Update a setting.
     */
    protected function updateSetting(string $key, $value): void
    {
        // This is a placeholder for actual database implementation
        // In a real application, you would update settings in a database table
        // For now, we'll just clear the cache to simulate an update
        Cache::forget('admin_settings');
    }
}
