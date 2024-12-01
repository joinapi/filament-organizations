<?php

namespace App\Actions\FilamentOrganizations;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Joinapi\FilamentOrganizations\Contracts\UpdatesOrganizationNames;

class UpdateOrganizationName implements UpdatesOrganizationNames
{
    /**
     * Validate and update the given organization's name.
     *
     * @param  array<string, string>  $input
     *
     * @throws AuthorizationException
     */
    public function update(User $user, Organization $organization, array $input): void
    {
        Gate::forUser($user)->authorize('update', $organization);

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
        ])->validateWithBag('updateOrganizationName');

        $organization->forceFill([
            'name' => $input['name'],
        ])->save();
    }
}
