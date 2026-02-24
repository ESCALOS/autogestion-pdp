<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

final class ApprovalPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'Approve:MachineryRequirement',
            'Reject:MachineryRequirement',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $this->command->info('✓ Permisos de aprobación creados exitosamente.');
    }
}
