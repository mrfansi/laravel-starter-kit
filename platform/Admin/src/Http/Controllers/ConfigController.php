<?php

namespace Platform\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Platform\Admin\Models\Configuration;

class ConfigController extends Controller
{
    /**
     * Display a listing of the configurations.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $group = $request->input('group');

        $query = Configuration::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                    ->orWhere('value', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($group) {
            $query->where('group', $group);
        }

        $configurations = $query->orderBy('group')->orderBy('key')->paginate(10);
        $groups = Configuration::distinct()->pluck('group');

        return view('admin::config.index', compact('configurations', 'groups', 'search', 'group'));
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
                'custom' => 'Custom',
            ];
        }

        // Define available data types
        $types = [
            'string' => 'String',
            'integer' => 'Integer',
            'float' => 'Float',
            'boolean' => 'Boolean',
            'array' => 'Array',
            'json' => 'JSON',
        ];

        return view('admin::config.create', compact('groupOptions', 'types'));
    }

    /**
     * Store a newly created configuration in storage.
     *
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
                'custom' => 'Custom',
            ];
        }

        // Define available data types
        $types = [
            'string' => 'String',
            'integer' => 'Integer',
            'float' => 'Float',
            'boolean' => 'Boolean',
            'array' => 'Array',
            'json' => 'JSON',
        ];

        return view('admin::config.edit', [
            'configuration' => $config,
            'groupOptions' => $groupOptions,
            'types' => $types,
        ]);
    }

    /**
     * Update the specified configuration in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Configuration $config)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255|unique:configurations,key,'.$config->id,
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
            // Get all configurations
            $configurations = Configuration::all();

            // Read current .env file
            $envPath = base_path('.env');
            $envContent = file_get_contents($envPath);
            $envLines = explode("\n", $envContent);

            // Create a backup of the current .env file
            $backupPath = base_path('.env.backup-'.date('Y-m-d-His'));
            file_put_contents($backupPath, $envContent);

            // Process each configuration
            foreach ($configurations as $config) {
                // Skip configurations that shouldn't be in .env
                if (strpos($config->key, '.') !== false) {
                    continue; // Skip config keys with dots
                }

                // Convert config key to ENV format (uppercase, underscores)
                $envKey = strtoupper(str_replace('.', '_', $config->key));

                // Format the value based on type
                $value = $this->formatValueForEnv($config->value, $config->type);

                // Check if the key already exists in .env
                $keyExists = false;
                foreach ($envLines as $i => $line) {
                    if (preg_match('/^'.preg_quote($envKey).'=/', $line)) {
                        $envLines[$i] = $envKey.'='.$value;
                        $keyExists = true;
                        break;
                    }
                }

                // If key doesn't exist, add it to the end
                if (! $keyExists) {
                    $envLines[] = $envKey.'='.$value;
                }
            }

            // Write the updated content back to .env
            file_put_contents($envPath, implode("\n", $envLines));

            return redirect()->route('admin.config.index')
                ->with('success', 'Configurations synced to .env file successfully. Backup created at '.$backupPath);
        } catch (\Exception $e) {
            return redirect()->route('admin.config.index')
                ->with('error', 'Failed to sync configurations to .env file: '.$e->getMessage());
        }
    }

    /**
     * Format the value for .env file based on its type.
     *
     * @param  mixed  $value
     * @param  string  $type
     * @return string
     */
    private function formatValueForEnv($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return $value ? 'true' : 'false';
            case 'integer':
                return (string) intval($value);
            case 'array':
            case 'json':
                if (is_string($value)) {
                    return $value;
                }

                return json_encode($value);
            default:
                // For strings, wrap in quotes if it contains spaces
                if (is_string($value) && strpos($value, ' ') !== false) {
                    return '"'.$value.'"';
                }

                return (string) $value;
        }
    }

    /**
     * Parse a value from .env file.
     *
     * @param  string  $value
     * @return mixed
     */
    private function parseEnvValue($value)
    {
        // Remove quotes if present
        if (strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) {
            $value = substr($value, 1, -1);
        } elseif (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1) {
            $value = substr($value, 1, -1);
        }

        // Convert special values
        if (strtolower($value) === 'true') {
            return true;
        } elseif (strtolower($value) === 'false') {
            return false;
        } elseif (strtolower($value) === 'null') {
            return null;
        } elseif (is_numeric($value)) {
            return strpos($value, '.') !== false ? (float) $value : (int) $value;
        }

        return $value;
    }

    /**
     * Guess the type of a value.
     *
     * @param  mixed  $value
     * @return string
     */
    private function guessValueType($value)
    {
        if (strtolower($value) === 'true' || strtolower($value) === 'false') {
            return 'boolean';
        } elseif (is_numeric($value) && strpos($value, '.') === false) {
            return 'integer';
        } elseif (strpos($value, '[') === 0 && strrpos($value, ']') === strlen($value) - 1) {
            return 'array';
        } elseif (strpos($value, '{') === 0 && strrpos($value, '}') === strlen($value) - 1) {
            return 'json';
        }

        return 'string';
    }

    /**
     * Sync configurations from .env file.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
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
                    [$key, $value] = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);

                    // Convert env key format to config key format
                    $configKey = strtolower(str_replace('_', '.', $key));

                    // Determine value type
                    $type = $this->guessValueType($value);

                    // Parse the value
                    $value = $this->parseEnvValue($value);

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
                ->with('error', 'Failed to sync configurations from .env file: '.$e->getMessage());
        }
    }

    /**
     * Import configurations from a JSON file.
     *
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
                throw new \Exception('Invalid JSON format: '.json_last_error_msg());
            }

            if (! is_array($configurations)) {
                throw new \Exception('Invalid configuration format. Expected an array of configurations.');
            }

            DB::beginTransaction();

            $imported = 0;
            $updated = 0;

            foreach ($configurations as $item) {
                // Validate each item
                if (! isset($item['key']) || ! isset($item['value']) || ! isset($item['type'])) {
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
                ->with('error', 'Failed to import configurations: '.$e->getMessage());
        }
    }
}
