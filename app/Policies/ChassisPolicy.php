<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Chassis;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChassisPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Chassis');
    }

    public function view(AuthUser $authUser, Chassis $chassis): bool
    {
        return $authUser->can('View:Chassis');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Chassis');
    }

    public function update(AuthUser $authUser, Chassis $chassis): bool
    {
        return $authUser->can('Update:Chassis');
    }

    public function delete(AuthUser $authUser, Chassis $chassis): bool
    {
        return $authUser->can('Delete:Chassis');
    }

}