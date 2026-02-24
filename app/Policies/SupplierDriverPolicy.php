<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\SupplierDriver;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

final class SupplierDriverPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SupplierDriver');
    }

    public function view(AuthUser $authUser, SupplierDriver $supplierDriver): bool
    {
        return $authUser->can('View:SupplierDriver');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SupplierDriver');
    }

    public function update(AuthUser $authUser, SupplierDriver $supplierDriver): bool
    {
        return $authUser->can('Update:SupplierDriver');
    }

    public function delete(AuthUser $authUser, SupplierDriver $supplierDriver): bool
    {
        return $authUser->can('Delete:SupplierDriver');
    }
}
