<?php

namespace Platform\Config\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Platform\Config\Facades\Config as ConfigFacade;
use Platform\Config\Models\Configuration;

class ConfigurationController extends Controller
{
    /**
     * Display a listing of the configurations.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $group = $request->query('group', 'general');
        $configurations = Configuration::where('group', $group)->paginate(20);
        $groups = config('platform.config.groups');
        
        return view('config::index', compact('configurations', 'groups', 'group'));
    }

    /**
     * Show the form for creating a new configuration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $groups = config('platform.config.groups');
        $types = [
            'string' => 'String',
            'boolean' => 'Boolean',
            'integer' => 'Integer',
            'array' => 'Array',
            'json' => 'JSON',
        ];
        
        return view('config::create', compact('groups', 'types'));
    }

    /**
     * Store a newly created configuration in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:configurations,key',
            'value' => 'nullable',
            'group' => 'required|string|max:255',
            'type' => 'required|string|in:string,boolean,integer,array,json',
            'description' => 'nullable|string',
            'is_system' => 'boolean',
            'is_public' => 'boolean',
        ]);
        
        // Format value based on type
        if ($validated['type'] === 'boolean') {
            $validated['value'] = (bool) $request->input('value', false);
        } elseif ($validated['type'] === 'integer') {
            $validated['value'] = (int) $request->input('value', 0);
        } elseif (in_array($validated['type'], ['array', 'json'])) {
            $validated['value'] = json_decode($request->input('value', '[]'), true) ?: [];
        }
        
        ConfigFacade::set(
            $validated['key'],
            $validated['value'],
            $validated['group'],
            $validated['type'],
            $validated['description'] ?? null,
            $validated['is_system'] ?? false,
            $validated['is_public'] ?? false
        );
        
        return redirect()->route('config.configurations.index', ['group' => $validated['group']])
            ->with('success', 'Configuration created successfully.');
    }

    /**
     * Show the form for editing the specified configuration.
     *
     * @param  \Platform\Config\Models\Configuration  $configuration
     * @return \Illuminate\View\View
     */
    public function edit(Configuration $configuration)
    {
        $groups = config('platform.config.groups');
        $types = [
            'string' => 'String',
            'boolean' => 'Boolean',
            'integer' => 'Integer',
            'array' => 'Array',
            'json' => 'JSON',
        ];
        
        return view('config::edit', compact('configuration', 'groups', 'types'));
    }

    /**
     * Update the specified configuration in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Platform\Config\Models\Configuration  $configuration
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Configuration $configuration)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:configurations,key,' . $configuration->id,
            'value' => 'nullable',
            'group' => 'required|string|max:255',
            'type' => 'required|string|in:string,boolean,integer,array,json',
            'description' => 'nullable|string',
            'is_system' => 'boolean',
            'is_public' => 'boolean',
        ]);
        
        // Format value based on type
        if ($validated['type'] === 'boolean') {
            $validated['value'] = (bool) $request->input('value', false);
        } elseif ($validated['type'] === 'integer') {
            $validated['value'] = (int) $request->input('value', 0);
        } elseif (in_array($validated['type'], ['array', 'json'])) {
            $validated['value'] = json_decode($request->input('value', '[]'), true) ?: [];
        }
        
        $configuration->update($validated);
        
        return redirect()->route('config.configurations.index', ['group' => $validated['group']])
            ->with('success', 'Configuration updated successfully.');
    }

    /**
     * Remove the specified configuration from storage.
     *
     * @param  \Platform\Config\Models\Configuration  $configuration
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Configuration $configuration)
    {
        $group = $configuration->group;
        $configuration->delete();
        
        return redirect()->route('config.configurations.index', ['group' => $group])
            ->with('success', 'Configuration deleted successfully.');
    }

    /**
     * Import configurations from a JSON file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:json',
        ]);
        
        $file = $request->file('import_file');
        $configs = json_decode(file_get_contents($file->path()), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->with('error', 'Invalid JSON file.');
        }
        
        ConfigFacade::import($configs);
        
        return redirect()->route('config.configurations.index')
            ->with('success', 'Configurations imported successfully.');
    }

    /**
     * Export all configurations as a JSON file.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export()
    {
        $configs = ConfigFacade::export();
        $json = json_encode($configs, JSON_PRETTY_PRINT);
        
        $tempFile = tempnam(sys_get_temp_dir(), 'config_export_');
        file_put_contents($tempFile, $json);
        
        return response()->download($tempFile, 'configurations_' . date('Y-m-d_His') . '.json', [
            'Content-Type' => 'application/json',
        ])->deleteFileAfterSend(true);
    }
}
