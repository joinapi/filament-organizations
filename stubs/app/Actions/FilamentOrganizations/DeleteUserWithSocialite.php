<?php

namespace App\Actions\FilamentOrganizations;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Joinapi\FilamentOrganizations\Contracts\DeletesOrganizations;
use Joinapi\FilamentOrganizations\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
    /**
     * The organization deleter implementation.
     */
    protected DeletesOrganizations $deletesOrganizations;

    /**
     * Create a new action instance.
     */
    public function __construct(DeletesOrganizations $deletesOrganizations)
    {
        $this->deletesOrganizations = $deletesOrganizations;
    }

    /**
     * Delete the given user.
     */
    public function delete(User $user): void
    {
        DB::transaction(function () use ($user) {
            $this->deleteOrganizations($user);
            $user->deleteProfilePhoto();
            $user->connectedAccounts->each(static fn ($account) => $account->delete());
            $user->tokens->each(static fn ($token) => $token->delete());
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
