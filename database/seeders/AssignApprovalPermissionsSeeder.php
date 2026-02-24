<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

final class AssignApprovalPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'Approve:MachineryRequirement',
            'Reject:MachineryRequirement',
        ];

        // Buscar o crear los permisos
        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // Asignar a super_admin si existe
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            foreach ($permissions as $permissionName) {
                $superAdminRole->givePermissionTo($permissionName);
            }
            $this->command->info("✓ Permisos asignados al rol 'super_admin'");
        }

        // Asignar a admin si existe
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
            $this->command->info("✓ Permisos asignados al rol 'admin'");
        }

        $this->command->info('✓ Asignación de permisos de aprobación completada.');
    }
}
