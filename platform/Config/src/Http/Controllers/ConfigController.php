<?php

namespace Platform\Config\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Platform\Config\Models\Configuration;
use Illuminate\Support\Facades\Validator;

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
        
        $configurations = $query->orderBy('group')->orderBy('key')->paginate(15);
        $groups = Configuration::distinct()->pluck('group');
        
        return view('config::index', compact('configurations', 'groups', 'search', 'group'));
    }
    
    /**
     * Show the form for creating a new configuration.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $groups = Configuration::distinct()->pluck('group');
        $types = ['string', 'boolean', 'integer', 'array', 'json'];
        
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
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255|unique:configurations',
            'value' => 'required',
            'type' => 'required|in:string,boolean,integer,array,json',
            'group' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $configuration = new Configuration();
        $configuration->key = $request->key;
        $configuration->value = $request->value;
        $configuration->type = $request->type;
        $configuration->group = $request->group;
        $configuration->description = $request->description;
        $configuration->save();
        
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
        $groups = Configuration::distinct()->pluck('group');
        $types = ['string', 'boolean', 'integer', 'array', 'json'];
        
        return view('config::edit', compact('config', 'groups', 'types'));
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
            'type' => 'required|in:string,boolean,integer,array,json',
            'group' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $config->key = $request->key;
        $config->value = $request->value;
        $config->type = $request->type;
        $config->group = $request->group;
        $config->description = $request->description;
        $config->save();
        
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
     * Sync configurations from database to .env file.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function syncToEnv()
    {
        // Get all configurations
        $configs = Configuration::all();
        
        // Read the current .env file
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);
        
        // Update each configuration in the .env file
        foreach ($configs as $config) {
            $key = strtoupper($config->key);
            $value = $this->formatValueForEnv($config->value, $config->type);
            
            // Check if the key already exists in the .env file
            if (preg_match("/^{$key}=.*$/m", $envContent)) {
                // Replace the existing value
                $envContent = preg_replace("/^{$key}=.*$/m", "{$key}={$value}", $envContent);
            } else {
                // Add the new key-value pair
                $envContent .= "\n{$key}={$value}";
            }
        }
        
        // Write the updated content back to the .env file
        file_put_contents($envPath, $envContent);
        
        return redirect()->route('admin.config.index')
            ->with('success', 'Configurations synced to .env file successfully.');
    }
    
    /**
     * Sync configurations from .env file to database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function syncFromEnv()
    {
        // Read the current .env file
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);
        
        // Parse the .env file
        $lines = explode("\n", $envContent);
        
        foreach ($lines as $line) {
            // Skip empty lines and comments
            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }
            
            // Parse key-value pairs
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Skip if key is empty
                if (empty($key)) {
                    continue;
                }
                
                // Check if the configuration already exists
                $config = Configuration::where('key', strtolower($key))->first();
                
                if ($config) {
                    // Update existing configuration
                    $config->value = $this->parseEnvValue($value);
                    $config->save();
                } else {
                    // Create new configuration
                    $config = new Configuration();
                    $config->key = strtolower($key);
                    $config->value = $this->parseEnvValue($value);
                    $config->type = $this->guessValueType($value);
                    $config->group = 'env';
                    $config->description = 'Imported from .env file';
                    $config->save();
                }
            }
        }
        
        return redirect()->route('admin.config.index')
            ->with('success', 'Configurations synced from .env file successfully.');
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
                    return '"' . $value . '"';
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
}
