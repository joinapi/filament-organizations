<?php

namespace App\Actions\FilamentOrganizations;

use Laravel\Socialite\Contracts\User;
use Laravel\Socialite\Facades\Socialite;
use Joinapi\FilamentOrganizations\Contracts\ResolvesSocialiteUsers;
use Joinapi\FilamentOrganizations\Enums\Feature;

class ResolveSocialiteUser implements ResolvesSocialiteUsers
{
    /**
     * Resolve the user for a given provider.
     */
    public function resolve(string $provider): User
    {
        $user = Socialite::driver($provider)->user();

        if (Feature::GenerateMissingEmails->isEnabled()) {
            $user->email = $user->getEmail() ?? ("{$user->id}@{$provider}" . config('app.domain'));
        }

        return $user;
    }
}
