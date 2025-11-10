<?php

namespace App\Enums;

enum RoleEnum: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case COMPANY_ADMIN = 'company_admin';
    case COMPANY_USER = 'company_user';

    public function label(): string
    {
        return match($this) {
            RoleEnum::SUPER_ADMIN => 'Super Administrador',
            RoleEnum::ADMIN => 'Administrador',
            RoleEnum::COMPANY_ADMIN => 'Administrador de Empresa',
            RoleEnum::COMPANY_USER => 'Usuario de Empresa',
        };
    }

    public static function allLabels(): array
    {
        return array_map(
            fn (RoleEnum $role) => $role->label(),
            self::cases()
        );
    }

}
