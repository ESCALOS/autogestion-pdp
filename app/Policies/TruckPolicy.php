<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Truck;
use Illuminate\Auth\Access\HandlesAuthorization;

class TruckPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Truck');
    }

    public function view(AuthUser $authUser, Truck $truck): bool
    {
        return $authUser->can('View:Truck');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Truck');
    }

    public function update(AuthUser $authUser, Truck $truck): bool
    {
        return $authUser->can('Update:Truck');
    }

    public function delete(AuthUser $authUser, Truck $truck): bool
    {
        return $authUser->can('Delete:Truck');
    }

}