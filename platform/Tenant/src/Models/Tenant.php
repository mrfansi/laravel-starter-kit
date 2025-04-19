<?php

namespace Platform\Tenant\Models;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;
    
    /**
     * Custom columns that should be added to the tenant model.
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'plan', // Plan yang digunakan tenant (misalnya: free, premium, enterprise)
            'trial_ends_at', // Tanggal berakhirnya trial
            'settings', // Pengaturan tenant dalam format JSON
        ];
    }
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'settings' => 'json',
    ];
}
