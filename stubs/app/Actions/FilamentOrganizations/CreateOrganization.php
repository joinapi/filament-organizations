<?php

namespace App\Actions\FilamentOrganizations;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Joinapi\FilamentOrganizations\Contracts\CreatesOrganizations;
use Joinapi\FilamentOrganizations\Events\AddingOrganization;
use Joinapi\FilamentOrganizations\FilamentOrganizations;

class CreateOrganization implements CreatesOrganizations
{
    /**
     * Validate and create a new organization for the given user.
     *
     * @param  array<string, string>  $input
     *
     * @throws AuthorizationException
     */
    public function create(User $user, array $input): Organization
    {
        Gate::forUser($user)->authorize('create', FilamentOrganizations::newOrganizationModel());

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
        ])->validateWithBag('createOrganization');

        AddingOrganization::dispatch($user);

        $user->switchOrganization($organization = $user->ownedOrganizations()->create([
            'name' => $input['name'],
            'personal_organization' => false,
        ]));

        return $organization;
    }
}
