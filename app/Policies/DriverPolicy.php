<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Driver;
use Illuminate\Auth\Access\HandlesAuthorization;

class DriverPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Driver');
    }

    public function view(AuthUser $authUser, Driver $driver): bool
    {
        return $authUser->can('View:Driver');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Driver');
    }

    public function update(AuthUser $authUser, Driver $driver): bool
    {
        return $authUser->can('Update:Driver');
    }

    public function delete(AuthUser $authUser, Driver $driver): bool
    {
        return $authUser->can('Delete:Driver');
    }

}