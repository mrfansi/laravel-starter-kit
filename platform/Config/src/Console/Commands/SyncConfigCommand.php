<?php

namespace Platform\Config\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Platform\Config\Facades\Config as DynamicConfig;

class SyncConfigCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'config:sync {--direction=env-to-db : Direction of sync (env-to-db or db-to-env)} {--force : Force overwrite of existing values}'
                           . ' {--group= : Only sync configs from a specific group}'
                           . ' {--dry-run : Show what would be synced without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync configurations between database and environment variables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $direction = $this->option('direction');
        $force = $this->option('force');
        $group = $this->option('group');
        $dryRun = $this->option('dry-run');

        if ($direction === 'env-to-db') {
            $this->syncEnvToDb($force, $group, $dryRun);
        } elseif ($direction === 'db-to-env') {
            $this->syncDbToEnv($force, $group, $dryRun);
        } else {
            $this->error("Invalid direction. Use 'env-to-db' or 'db-to-env'.");
            return 1;
        }

        return 0;
    }

    /**
     * Sync environment variables to database.
     *
     * @param bool $force
     * @param string|null $group
     * @param bool $dryRun
     * @return void
     */
    protected function syncEnvToDb(bool $force, ?string $group, bool $dryRun): void
    {
        $this->info("Syncing environment variables to database..." . ($dryRun ? ' (DRY RUN)' : ''));

        // Get all environment variables
        $envVariables = $_ENV;
        $dotenv = parse_ini_file(base_path('.env'));
        if ($dotenv) {
            $envVariables = array_merge($envVariables, $dotenv);
        }

        $count = 0;
        foreach ($envVariables as $key => $value) {
            // Skip if not in the specified group
            if ($group && !$this->keyBelongsToGroup($key, $group)) {
                continue;
            }

            // Determine the configuration group and type
            $configGroup = $this->determineGroup($key);
            $configType = $this->determineType($value);

            // Skip if the configuration already exists and force is not enabled
            if (DynamicConfig::has($key) && !$force) {
                $this->line("Skipping existing configuration: {$key}");
                continue;
            }

            // Format the value based on the type
            $formattedValue = $this->formatValue($value, $configType);

            if ($dryRun) {
                $this->line("Would sync: {$key} = {$formattedValue} (Type: {$configType}, Group: {$configGroup})");
            } else {
                DynamicConfig::set(
                    $key,
                    $formattedValue,
                    $configGroup,
                    $configType,
                    "Imported from environment variable",
                    true,
                    false
                );
                $this->line("Synced: {$key}");
            }

            $count++;
        }

        $this->info("Synced {$count} environment variables to database." . ($dryRun ? ' (DRY RUN)' : ''));
    }

    /**
     * Sync database configurations to environment file.
     *
     * @param bool $force
     * @param string|null $group
     * @param bool $dryRun
     * @return void
     */
    protected function syncDbToEnv(bool $force, ?string $group, bool $dryRun): void
    {
        $this->info("Syncing database configurations to .env file..." . ($dryRun ? ' (DRY RUN)' : ''));

        // Get configurations from database
        $query = DynamicConfig::getAllByGroup($group);
        
        if (empty($query)) {
            $this->warn("No configurations found in the database.");
            return;
        }

        // Read current .env file
        $envPath = base_path('.env');
        $envContent = File::exists($envPath) ? File::get($envPath) : '';
        $envLines = explode("\n", $envContent);
        $envVars = [];

        // Parse current .env variables
        foreach ($envLines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }

            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $envVars[trim($parts[0])] = trim($parts[1]);
            }
        }

        $count = 0;
        $newEnvContent = '';

        // Process each configuration
        foreach ($query as $key => $value) {
            // Format the value for .env file
            $envValue = $this->formatValueForEnv($value);

            // Skip if the variable already exists and force is not enabled
            if (isset($envVars[$key]) && !$force) {
                $this->line("Skipping existing variable: {$key}");
                continue;
            }

            if ($dryRun) {
                $this->line("Would set: {$key}={$envValue}");
            } else {
                $envVars[$key] = $envValue;
            }

            $count++;
        }

        if (!$dryRun && $count > 0) {
            // Build new .env content
            foreach ($envVars as $key => $value) {
                $newEnvContent .= "{$key}={$value}\n";
            }

            // Backup the original .env file
            if (File::exists($envPath)) {
                $backupPath = base_path('.env.backup-' . date('YmdHis'));
                File::copy($envPath, $backupPath);
                $this->info("Backed up original .env file to {$backupPath}");
            }

            // Write the new .env file
            File::put($envPath, $newEnvContent);
            $this->info("Updated .env file with {$count} configurations.");
        } else {
            $this->info("Would update .env file with {$count} configurations." . ($dryRun ? ' (DRY RUN)' : ''));
        }
    }

    /**
     * Determine if a key belongs to a specific group.
     *
     * @param string $key
     * @param string $group
     * @return bool
     */
    protected function keyBelongsToGroup(string $key, string $group): bool
    {
        $keyGroup = $this->determineGroup($key);
        return strtolower($keyGroup) === strtolower($group);
    }

    /**
     * Determine the group for a configuration key.
     *
     * @param string $key
     * @return string
     */
    protected function determineGroup(string $key): string
    {
        $key = strtolower($key);

        if (strpos($key, 'app_') === 0) {
            return 'app';
        } elseif (strpos($key, 'db_') === 0) {
            return 'database';
        } elseif (strpos($key, 'mail_') === 0) {
            return 'mail';
        } elseif (strpos($key, 'queue_') === 0) {
            return 'queue';
        } elseif (strpos($key, 'cache_') === 0) {
            return 'cache';
        } elseif (strpos($key, 'session_') === 0) {
            return 'session';
        } elseif (strpos($key, 'auth_') === 0) {
            return 'auth';
        } elseif (strpos($key, 'log_') === 0) {
            return 'logging';
        } else {
            return 'general';
        }
    }

    /**
     * Determine the type of a configuration value.
     *
     * @param mixed $value
     * @return string
     */
    protected function determineType(mixed $value): string
    {
        if (is_bool($value) || in_array(strtolower($value), ['true', 'false', '0', '1', 'yes', 'no', 'on', 'off'])) {
            return 'boolean';
        } elseif (is_numeric($value)) {
            return 'integer';
        } elseif (is_array($value)) {
            return 'array';
        } elseif ($this->isJson($value)) {
            return 'json';
        } else {
            return 'string';
        }
    }

    /**
     * Format a value based on its type.
     *
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    protected function formatValue(mixed $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'array', 'json' => is_string($value) && $this->isJson($value) ? json_decode($value, true) : $value,
            default => (string) $value,
        };
    }

    /**
     * Format a value for the .env file.
     *
     * @param mixed $value
     * @return string
     */
    protected function formatValueForEnv(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        } elseif (is_array($value) || is_object($value)) {
            return '"' . json_encode($value) . '"';
        } elseif (is_string($value) && (strpos($value, ' ') !== false || strpos($value, '#') !== false)) {
            return '"' . $value . '"';
        } else {
            return (string) $value;
        }
    }

    /**
     * Check if a string is valid JSON.
     *
     * @param string $string
     * @return bool
     */
    protected function isJson(string $string): bool
    {
        if (!is_string($string)) {
            return false;
        }
        
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
