<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\SupplierMachinery;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

final class SupplierMachineryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SupplierMachinery');
    }

    public function view(AuthUser $authUser, SupplierMachinery $supplierMachinery): bool
    {
        return $authUser->can('View:SupplierMachinery');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SupplierMachinery');
    }

    public function update(AuthUser $authUser, SupplierMachinery $supplierMachinery): bool
    {
        return $authUser->can('Update:SupplierMachinery');
    }

    public function delete(AuthUser $authUser, SupplierMachinery $supplierMachinery): bool
    {
        return $authUser->can('Delete:SupplierMachinery');
    }
}
