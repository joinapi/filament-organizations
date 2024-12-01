<?php

namespace App\Actions\FilamentOrganizations;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Contracts\User as ProviderUserContract;
use Joinapi\FilamentOrganizations\Contracts\CreatesConnectedAccounts;
use Joinapi\FilamentOrganizations\Contracts\CreatesUserFromProvider;
use Joinapi\FilamentOrganizations\Enums\Feature;
use Joinapi\FilamentOrganizations\FilamentOrganizations;

class CreateUserFromProvider implements CreatesUserFromProvider
{
    /**
     * The creates connected accounts instance.
     */
    public CreatesConnectedAccounts $createsConnectedAccounts;

    /**
     * Create a new action instance.
     */
    public function __construct(CreatesConnectedAccounts $createsConnectedAccounts)
    {
        $this->createsConnectedAccounts = $createsConnectedAccounts;
    }

    /**
     * Create a new user from a social provider user.
     */
    public function create(string $provider, ProviderUserContract $providerUser): User
    {
        return DB::transaction(function () use ($providerUser, $provider) {
            return tap(User::create([
                'name' => $providerUser->getName(),
                'email' => $providerUser->getEmail(),
            ]), function (User $user) use ($providerUser, $provider) {
                $user->markEmailAsVerified();

                if ($this->shouldSetProfilePhoto($providerUser)) {
                    $user->setProfilePhotoFromUrl($providerUser->getAvatar());
                }

                $user->switchConnectedAccount(
                    $this->createsConnectedAccounts->create($user, $provider, $providerUser)
                );

                $this->createOrganization($user);
            });
        });
    }

    private function shouldSetProfilePhoto(ProviderUserContract $providerUser): bool
    {
        return Feature::ProviderAvatars->isEnabled() &&
            FilamentOrganizations::managesProfilePhotos() &&
            $providerUser->getAvatar();
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
