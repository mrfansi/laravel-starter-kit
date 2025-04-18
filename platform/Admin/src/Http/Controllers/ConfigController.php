<?php

namespace Platform\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Platform\Config\Models\Configuration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

class ConfigController extends Controller
{
    /**
     * Display a listing of the configurations.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $group = $request->query('group');
        $query = Configuration::query();
        
        if ($group) {
            $query->where('group', $group);
        }
        
        $configurations = $query->orderBy('group')->orderBy('key')->paginate(10);
        $groups = Configuration::distinct()->pluck('group');
        
        return view('config::index', compact('configurations', 'groups', 'group'));
    }

    /**
     * Show the form for creating a new configuration.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Get distinct groups for the dropdown
        $groups = Configuration::distinct()->pluck('group');
        $groupOptions = [];
        
        // Create an array of group options
        foreach ($groups as $group) {
            $groupOptions[$group] = ucfirst($group);
        }
        
        // Add some default groups if none exist
        if (empty($groupOptions)) {
            $groupOptions = [
                'app' => 'App',
                'mail' => 'Mail',
                'database' => 'Database',
                'cache' => 'Cache',
                'queue' => 'Queue',
                'services' => 'Services',
                'custom' => 'Custom'
            ];
        }
        
        // Define available data types
        $types = [
            'string' => 'String',
            'integer' => 'Integer',
            'float' => 'Float',
            'boolean' => 'Boolean',
            'array' => 'Array',
            'json' => 'JSON'
        ];
        
        return view('config::create', compact('groupOptions', 'types'));
    }

    /**
     * Store a newly created configuration in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Check if this is a file import request
        if ($request->hasFile('import_file')) {
            return $this->importFromJson($request);
        }
        
        // Regular configuration creation
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255|unique:configurations,key',
            'value' => 'required',
            'type' => 'required|string|in:string,integer,float,boolean,array,json',
            'group' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Process the value based on type
        $value = $request->value;
        switch ($request->type) {
            case 'integer':
                $value = (int) $value;
                break;
            case 'float':
                $value = (float) $value;
                break;
            case 'boolean':
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                break;
            case 'array':
            case 'json':
                $value = json_decode($value, true);
                break;
        }

        Configuration::create([
            'key' => $request->key,
            'value' => $value,
            'type' => $request->type,
            'group' => $request->group,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.config.index')
            ->with('success', 'Configuration created successfully.');
    }

    /**
     * Show the form for editing the specified configuration.
     *
     * @param  \Platform\Config\Models\Configuration  $config
     * @return \Illuminate\View\View
     */
    public function edit(Configuration $config)
    {
        // Get distinct groups for the dropdown
        $groups = Configuration::distinct()->pluck('group');
        $groupOptions = [];
        
        // Create an array of group options
        foreach ($groups as $group) {
            $groupOptions[$group] = ucfirst($group);
        }
        
        // Add some default groups if none exist
        if (empty($groupOptions)) {
            $groupOptions = [
                'app' => 'App',
                'mail' => 'Mail',
                'database' => 'Database',
                'cache' => 'Cache',
                'queue' => 'Queue',
                'services' => 'Services',
                'custom' => 'Custom'
            ];
        }
        
        // Define available data types
        $types = [
            'string' => 'String',
            'integer' => 'Integer',
            'float' => 'Float',
            'boolean' => 'Boolean',
            'array' => 'Array',
            'json' => 'JSON'
        ];
        
        return view('config::edit', [
            'configuration' => $config,
            'groupOptions' => $groupOptions,
            'types' => $types
        ]);
    }

    /**
     * Update the specified configuration in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Platform\Config\Models\Configuration  $config
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Configuration $config)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255|unique:configurations,key,' . $config->id,
            'value' => 'required',
            'type' => 'required|string|in:string,integer,float,boolean,array,json',
            'group' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Process the value based on type
        $value = $request->value;
        switch ($request->type) {
            case 'integer':
                $value = (int) $value;
                break;
            case 'float':
                $value = (float) $value;
                break;
            case 'boolean':
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                break;
            case 'array':
            case 'json':
                $value = json_decode($value, true);
                break;
        }

        $config->update([
            'key' => $request->key,
            'value' => $value,
            'type' => $request->type,
            'group' => $request->group,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.config.index')
            ->with('success', 'Configuration updated successfully.');
    }

    /**
     * Remove the specified configuration from storage.
     *
     * @param  \Platform\Config\Models\Configuration  $config
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Configuration $config)
    {
        $config->delete();

        return redirect()->route('admin.config.index')
            ->with('success', 'Configuration deleted successfully.');
    }

    /**
     * Sync configurations to .env file.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function syncToEnv()
    {
        try {
            $configurations = Configuration::all();
            $envContent = file_get_contents(base_path('.env'));
            
            foreach ($configurations as $config) {
                $key = strtoupper(str_replace('.', '_', $config->key));
                $value = $config->value;
                
                // Format the value based on type
                if (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                } elseif (is_array($value) || is_object($value)) {
                    $value = json_encode($value);
                }
                
                // Escape quotes in the value
                $value = is_string($value) ? '"' . str_replace('"', '\\"', $value) . '"' : $value;
                
                // Check if the key already exists in the .env file
                if (preg_match('/^' . $key . '=.*$/m', $envContent)) {
                    // Update existing key
                    $envContent = preg_replace('/^' . $key . '=.*$/m', $key . '=' . $value, $envContent);
                } else {
                    // Add new key
                    $envContent .= "\n" . $key . '=' . $value;
                }
            }
            
            file_put_contents(base_path('.env'), $envContent);
            
            return redirect()->route('admin.config.index')
                ->with('success', 'Configurations synced to .env file successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.config.index')
                ->with('error', 'Failed to sync configurations to .env file: ' . $e->getMessage());
        }
    }

    /**
     * Sync configurations from .env file.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    /**
     * Import configurations from a JSON file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function importFromJson(Request $request)
    {
        // Validate the uploaded file
        $validator = Validator::make($request->all(), [
            'import_file' => 'required|file|mimes:json|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.config.index')
                ->withErrors($validator);
        }

        try {
            // Read the JSON file
            $jsonContent = file_get_contents($request->file('import_file')->path());
            $configurations = json_decode($jsonContent, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON format: ' . json_last_error_msg());
            }
            
            if (!is_array($configurations)) {
                throw new \Exception('Invalid configuration format. Expected an array of configurations.');
            }
            
            DB::beginTransaction();
            
            $imported = 0;
            $updated = 0;
            
            foreach ($configurations as $item) {
                // Validate each item
                if (!isset($item['key']) || !isset($item['value']) || !isset($item['type'])) {
                    continue;
                }
                
                // Process the value based on type
                $value = $item['value'];
                switch ($item['type']) {
                    case 'integer':
                        $value = (int) $value;
                        break;
                    case 'float':
                        $value = (float) $value;
                        break;
                    case 'boolean':
                        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                        break;
                    case 'array':
                    case 'json':
                        if (is_string($value)) {
                            $value = json_decode($value, true);
                        }
                        break;
                }
                
                // Check if configuration already exists
                $config = Configuration::where('key', $item['key'])->first();
                
                if ($config) {
                    // Update existing configuration
                    $config->update([
                        'value' => $value,
                        'type' => $item['type'],
                        'group' => $item['group'] ?? $config->group,
                        'description' => $item['description'] ?? $config->description,
                    ]);
                    $updated++;
                } else {
                    // Create new configuration
                    Configuration::create([
                        'key' => $item['key'],
                        'value' => $value,
                        'type' => $item['type'],
                        'group' => $item['group'] ?? 'imported',
                        'description' => $item['description'] ?? 'Imported from JSON file',
                    ]);
                    $imported++;
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.config.index')
                ->with('success', "Import successful: {$imported} configurations created, {$updated} configurations updated.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.config.index')
                ->with('error', 'Failed to import configurations: ' . $e->getMessage());
        }
    }

    public function syncFromEnv()
    {
        try {
            $envContent = file_get_contents(base_path('.env'));
            $lines = explode("\n", $envContent);
            
            DB::beginTransaction();
            
            foreach ($lines as $line) {
                // Skip comments and empty lines
                if (empty($line) || strpos($line, '#') === 0) {
                    continue;
                }
                
                // Parse key and value
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    
                    // Convert env key format to config key format
                    $configKey = strtolower(str_replace('_', '.', $key));
                    
                    // Determine value type
                    $type = 'string';
                    if ($value === 'true' || $value === 'false') {
                        $type = 'boolean';
                        $value = ($value === 'true');
                    } elseif (is_numeric($value) && strpos($value, '.') !== false) {
                        $type = 'float';
                        $value = (float) $value;
                    } elseif (is_numeric($value)) {
                        $type = 'integer';
                        $value = (int) $value;
                    } elseif (strpos($value, '[') === 0 || strpos($value, '{') === 0) {
                        $type = 'json';
                        $value = json_decode($value, true);
                    }
                    
                    // Remove quotes if present
                    if (is_string($value)) {
                        if ((strpos($value, '"') === 0 && substr($value, -1) === '"') || 
                            (strpos($value, "'") === 0 && substr($value, -1) === "'")) {
                            $value = substr($value, 1, -1);
                        }
                    }
                    
                    // Check if configuration already exists
                    $config = Configuration::where('key', $configKey)->first();
                    
                    if ($config) {
                        // Update existing configuration
                        $config->update([
                            'value' => $value,
                            'type' => $type,
                        ]);
                    } else {
                        // Create new configuration
                        Configuration::create([
                            'key' => $configKey,
                            'value' => $value,
                            'type' => $type,
                            'group' => 'env',
                            'description' => 'Imported from .env file',
                        ]);
                    }
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.config.index')
                ->with('success', 'Configurations synced from .env file successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.config.index')
                ->with('error', 'Failed to sync configurations from .env file: ' . $e->getMessage());
        }
    }
}
