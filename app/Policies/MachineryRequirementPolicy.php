<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\MachineryRequirement;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

final class MachineryRequirementPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MachineryRequirement');
    }

    public function view(AuthUser $authUser, MachineryRequirement $machineryRequirement): bool
    {
        return $authUser->can('View:MachineryRequirement');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MachineryRequirement');
    }

    public function update(AuthUser $authUser, MachineryRequirement $machineryRequirement): bool
    {
        return $authUser->can('Update:MachineryRequirement');
    }

    public function delete(AuthUser $authUser, MachineryRequirement $machineryRequirement): bool
    {
        return $authUser->can('Delete:MachineryRequirement');
    }
}
