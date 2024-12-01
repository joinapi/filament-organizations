<?php

namespace App\Actions\FilamentOrganizations;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Joinapi\FilamentOrganizations\Contracts\CreatesNewUsers;
use Joinapi\FilamentOrganizations\FilamentOrganizations;

class CreateNewUser implements CreatesNewUsers
{
    /**
     * Create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => FilamentOrganizations::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        return DB::transaction(function () use ($input) {
            return tap(User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]), function (User $user) {
                $this->createOrganization($user);
            });
        });
    }

    /**
     * Create a personal organization for the user.
     */
    protected function createOrganization(User $user): void
    {
        $user->ownedOrganizations()->save(Organization::forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->name, 2)[0] . "'s Organization",
            'personal_organization' => true,
        ]));
    }
}
