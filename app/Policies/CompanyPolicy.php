<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Company;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Company');
    }

    public function view(AuthUser $authUser, Company $company): bool
    {
        return $authUser->can('View:Company');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Company');
    }

    public function update(AuthUser $authUser, Company $company): bool
    {
        return $authUser->can('Update:Company');
    }

    public function delete(AuthUser $authUser, Company $company): bool
    {
        return $authUser->can('Delete:Company');
    }

}