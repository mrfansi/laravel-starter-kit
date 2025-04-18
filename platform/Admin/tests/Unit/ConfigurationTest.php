<?php

use Platform\Admin\Models\Configuration;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(TestCase::class);
uses(RefreshDatabase::class);

test('configuration can be created', function () {
    $config = Configuration::factory()->create([
        'key' => 'site_name',
        'value' => 'Laravel Starter Kit',
        'group' => 'general',
        'type' => 'string',
        'description' => 'The name of the site',
        'is_system' => true,
        'is_public' => true,
    ]);
    
    expect($config)->toBeInstanceOf(Configuration::class)
        ->and($config->key)->toBe('site_name')
        ->and($config->value)->toBe('Laravel Starter Kit')
        ->and($config->group)->toBe('general')
        ->and($config->type)->toBe('string')
        ->and($config->description)->toBe('The name of the site')
        ->and($config->is_system)->toBeTrue()
        ->and($config->is_public)->toBeTrue();
});

test('configuration value is cast based on type', function () {
    // Test string type
    $stringConfig = Configuration::factory()->create([
        'key' => 'app_name',
        'value' => 'My App',
        'type' => 'string',
    ]);
    expect($stringConfig->value)->toBe('My App')->toBeString();
    
    // Test boolean type
    $boolConfig = Configuration::factory()->create([
        'key' => 'maintenance_mode',
        'value' => true,
        'type' => 'boolean',
    ]);
    expect($boolConfig->value)->toBeTrue()->toBeBool();
    
    // Test integer type
    $intConfig = Configuration::factory()->create([
        'key' => 'items_per_page',
        'value' => 10,
        'type' => 'integer',
    ]);
    expect($intConfig->value)->toBe(10)->toBeInt();
    
    // Test array/json type
    $arrayData = ['key1' => 'value1', 'key2' => 'value2'];
    $arrayConfig = Configuration::factory()->create([
        'key' => 'allowed_domains',
        'value' => $arrayData,
        'type' => 'array',
    ]);
    expect($arrayConfig->value)->toBe($arrayData)->toBeArray();
});

test('configuration value is properly formatted when saving', function () {
    // Test array conversion to JSON string
    $arrayData = ['site' => 'Laravel Starter Kit', 'version' => '1.0.0'];
    $config = new Configuration([
        'key' => 'site_info',
        'type' => 'array',
        'value' => $arrayData,
    ]);
    $config->save();
    
    // Retrieve from database to verify storage format
    $retrievedConfig = Configuration::where('key', 'site_info')->first();
    
    // Check the raw value in the database (should be a JSON string)
    $rawValue = DB::table('configurations')
        ->where('key', 'site_info')
        ->value('value');
    
    expect($rawValue)->toBeString()
        ->and(json_decode($rawValue, true))->toBe($arrayData)
        ->and($retrievedConfig->value)->toBe($arrayData); // But the accessor should convert it back to an array
});

test('configuration factory creates valid instances', function () {
    // Test basic factory
    $config = Configuration::factory()->create();
    expect($config)->toBeInstanceOf(Configuration::class)
        ->and($config->key)->not->toBeEmpty()
        ->and($config->group)->not->toBeEmpty();
    
    // Test system configuration factory
    $systemConfig = Configuration::factory()->system()->create();
    expect($systemConfig->is_system)->toBeTrue();
    
    // Test public configuration factory
    $publicConfig = Configuration::factory()->public()->create();
    expect($publicConfig->is_public)->toBeTrue();
    
    // Test specific type factory
    $boolConfig = Configuration::factory()->ofType('boolean')->create();
    expect($boolConfig->type)->toBe('boolean')
        ->and($boolConfig->value)->toBeBool();
});
