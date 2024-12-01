<?php

namespace Joinapi\FilamentOrganizations\Actions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Joinapi\FilamentOrganizations\Events\OrganizationEmployeeUpdated;
use Joinapi\FilamentOrganizations\FilamentOrganizations;
use Joinapi\FilamentOrganizations\Rules\Role;

class UpdateOrganizationEmployeeRole
{
    /**
     * Update the role for the given organization employee.
     *
     * @throws AuthorizationException
     */
    public function update(mixed $user, mixed $organization, int $organizationEmployeeId, string $role): void
    {
        Gate::forUser($user)->authorize('updateOrganizationEmployee', $organization);

        Validator::make(compact('role'), [
            'role' => ['required', 'string', new Role],
        ])->validate();

        $organization->users()->updateExistingPivot($organizationEmployeeId, compact('role'));

        OrganizationEmployeeUpdated::dispatch($organization->fresh(), FilamentOrganizations::findUserByIdOrFail($organizationEmployeeId));
    }
}
