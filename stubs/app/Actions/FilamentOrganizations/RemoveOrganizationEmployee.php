<?php

namespace App\Actions\FilamentOrganizations;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Joinapi\FilamentOrganizations\Contracts\RemovesOrganizationEmployees;
use Joinapi\FilamentOrganizations\Events\OrganizationEmployeeRemoved;

class RemoveOrganizationEmployee implements RemovesOrganizationEmployees
{
    /**
     * Remove the organization employee from the given organization.
     *
     * @throws AuthorizationException
     */
    public function remove(User $user, Organization $organization, User $organizationEmployee): void
    {
        $this->authorize($user, $organization, $organizationEmployee);

        $this->ensureUserDoesNotOwnOrganization($organizationEmployee, $organization);

        $organization->removeUser($organizationEmployee);

        OrganizationEmployeeRemoved::dispatch($organization, $organizationEmployee);
    }

    /**
     * Authorize that the user can remove the organization employee.
     *
     * @throws AuthorizationException
     */
    protected function authorize(User $user, Organization $organization, User $organizationEmployee): void
    {
        if (! Gate::forUser($user)->check('removeOrganizationEmployee', $organization) &&
            $user->id !== $organizationEmployee->id) {
            throw new AuthorizationException;
        }
    }

    /**
     * Ensure that the currently authenticated user does not own the organization.
     */
    protected function ensureUserDoesNotOwnOrganization(User $organizationEmployee, Organization $organization): void
    {
        if ($organizationEmployee->id === $organization->owner->id) {
            throw ValidationException::withMessages([
                'organization' => [__('filament-organizations::default.errors.cannot_leave_organization')],
            ])->errorBag('removeOrganizationEmployee');
        }
    }
}
