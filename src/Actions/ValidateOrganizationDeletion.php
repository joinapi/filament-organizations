<?php

namespace Joinapi\FilamentOrganizations\Actions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class ValidateOrganizationDeletion
{
    /**
     * Validate that the organization can be deleted by the given user.
     *
     * @throws AuthorizationException
     */
    public function validate(mixed $user, mixed $organization): void
    {
        Gate::forUser($user)->authorize('delete', $organization);

        if ($organization->personal_organization) {
            throw ValidationException::withMessages([
                'organization' => __('filament-organizations::default.errors.organization_deletion'),
            ])->errorBag('deleteOrganization');
        }
    }
}
