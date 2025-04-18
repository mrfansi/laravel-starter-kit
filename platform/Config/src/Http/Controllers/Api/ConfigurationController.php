<?php

namespace Platform\Config\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Platform\Config\Facades\Config as ConfigFacade;
use Platform\Config\Models\Configuration;

class ConfigurationController extends Controller
{
    /**
     * Display a listing of the public configurations.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $group = $request->query('group');
        
        $query = Configuration::where('is_public', true);
        
        if ($group) {
            $query->where('group', $group);
        }
        
        $configurations = $query->get();
        
        $result = [];
        foreach ($configurations as $config) {
            $result[$config->key] = $config->value;
        }
        
        return response()->json(['data' => $result]);
    }

    /**
     * Get a specific public configuration.
     *
     * @param  string  $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $key)
    {
        $config = Configuration::where('key', $key)
            ->where('is_public', true)
            ->first();
        
        if (!$config) {
            return response()->json(['error' => 'Configuration not found'], 404);
        }
        
        return response()->json(['data' => [
            'key' => $config->key,
            'value' => $config->value,
            'group' => $config->group,
            'type' => $config->type,
            'description' => $config->description,
        ]]);
    }

    /**
     * Update a configuration (admin only).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $key)
    {
        $config = Configuration::where('key', $key)->first();
        
        if (!$config) {
            return response()->json(['error' => 'Configuration not found'], 404);
        }
        
        $validated = $request->validate([
            'value' => 'required',
            'group' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'is_public' => 'sometimes|boolean',
        ]);
        
        // Format value based on type
        if ($config->type === 'boolean') {
            $validated['value'] = (bool) $validated['value'];
        } elseif ($config->type === 'integer') {
            $validated['value'] = (int) $validated['value'];
        } elseif (in_array($config->type, ['array', 'json']) && is_string($validated['value'])) {
            $validated['value'] = json_decode($validated['value'], true) ?: [];
        }
        
        $config->update($validated);
        
        return response()->json([
            'data' => $config,
            'message' => 'Configuration updated successfully',
        ]);
    }

    /**
     * Batch update configurations (admin only).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function batchUpdate(Request $request)
    {
        $validated = $request->validate([
            'configs' => 'required|array',
            'configs.*.key' => 'required|string|exists:configurations,key',
            'configs.*.value' => 'required',
        ]);
        
        $updated = [];
        
        foreach ($validated['configs'] as $configData) {
            $config = Configuration::where('key', $configData['key'])->first();
            
            if ($config) {
                // Format value based on type
                $value = $configData['value'];
                if ($config->type === 'boolean') {
                    $value = (bool) $value;
                } elseif ($config->type === 'integer') {
                    $value = (int) $value;
                } elseif (in_array($config->type, ['array', 'json']) && is_string($value)) {
                    $value = json_decode($value, true) ?: [];
                }
                
                $config->update(['value' => $value]);
                $updated[] = $config;
            }
        }
        
        return response()->json([
            'data' => $updated,
            'message' => count($updated) . ' configurations updated successfully',
        ]);
    }
}
