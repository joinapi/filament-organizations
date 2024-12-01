<?php

namespace App\Actions\FilamentOrganizations;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;
use Joinapi\FilamentOrganizations\Contracts\DeletesOrganizations;
use Joinapi\FilamentOrganizations\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
    /**
     * Create a new action instance.
     */
    public function __construct(protected DeletesOrganizations $deletesOrganizations)
    {
        //
    }

    /**
     * Delete the given user.
     */
    public function delete(User $user): void
    {
        DB::transaction(function () use ($user) {
            $this->deleteOrganizations($user);
            $user->deleteProfilePhoto();
            $user->tokens->each(static fn (PersonalAccessToken $token) => $token->delete());
            $user->delete();
        });
    }

    /**
     * Delete the organizations and organization associations attached to the user.
     */
    protected function deleteOrganizations(User $user): void
    {
        $user->organizations()->detach();

        $user->ownedOrganizations->each(function (Organization $organization) {
            $this->deletesOrganizations->delete($organization);
        });
    }
}
