<?php

namespace Platform\Tenant\Console\Commands;

use Platform\Tenant\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create {name} {domain} {--plan=free} {--trial-days=14}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new tenant with domain';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $domain = $this->argument('domain');
        $plan = $this->option('plan');
        $trialDays = $this->option('trial-days');

        // Generate a UUID for the tenant
        $id = Str::uuid()->toString();

        // Calculate trial end date
        $trialEndsAt = now()->addDays($trialDays);

        // Create the tenant
        $tenant = Tenant::create([
            'id' => $id,
            'name' => $name,
            'plan' => $plan,
            'trial_ends_at' => $trialEndsAt,
            'settings' => [
                'theme' => 'default',
                'notifications' => true,
            ],
        ]);

        // Create domain for the tenant
        $tenant->domains()->create([
            'domain' => $domain,
        ]);

        $this->info('Tenant created successfully!');
        $this->table(
            ['ID', 'Name', 'Domain', 'Plan', 'Trial Ends At'],
            [[
                $tenant->id,
                $tenant->name,
                $domain,
                $tenant->plan,
                $tenant->trial_ends_at->format('Y-m-d'),
            ]]
        );

        return self::SUCCESS;
    }
}
