<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganizationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Organization $organization): bool
    {
        return $user->belongsToOrganization($organization);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Organization $organization): bool
    {
        return $user->ownsOrganization($organization);
    }

    /**
     * Determine whether the user can add organization employees.
     */
    public function addOrganizationEmployee(User $user, Organization $organization): bool
    {
        return $user->ownsOrganization($organization);
    }

    /**
     * Determine whether the user can update organization employee permissions.
     */
    public function updateOrganizationEmployee(User $user, Organization $organization): bool
    {
        return $user->ownsOrganization($organization);
    }

    /**
     * Determine whether the user can remove organization employees.
     */
    public function removeOrganizationEmployee(User $user, Organization $organization): bool
    {
        return $user->ownsOrganization($organization);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Organization $organization): bool
    {
        return $user->ownsOrganization($organization);
    }
}
