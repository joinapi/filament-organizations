<?php

namespace Joinapi\FilamentOrganizations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

abstract class Organization extends Model
{
    /**
     * Get the owner of the organization.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(FilamentOrganizations::userModel(), 'user_id');
    }

    /**
     * Get all the organization's users including its owner.
     */
    public function allUsers(): Collection
    {
        return $this->users->merge([$this->owner]);
    }

    /**
     * Get all the users that belong to the organization.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(FilamentOrganizations::userModel(), FilamentOrganizations::employeeshipModel())
            ->withPivot('role')
            ->withTimestamps()
            ->as('employeeship');
    }

    /**
     * Determine if the given user belongs to the organization.
     */
    public function hasUser(mixed $user): bool
    {
        return $this->users->contains($user) || $user->ownsOrganization($this);
    }

    /**
     * Determine if the given email address belongs to a user on the organization.
     */
    public function hasUserWithEmail(string $email): bool
    {
        return $this->allUsers()->contains(static function ($user) use ($email) {
            return $user->email === $email;
        });
    }

    /**
     * Determine if the given user has the given permission on the organization.
     */
    public function userHasPermission(mixed $user, string $permission): bool
    {
        return $user->hasOrganizationPermission($this, $permission);
    }

    /**
     * Get all the pending user invitations for the organization.
     */
    public function organizationInvitations(): HasMany
    {
        return $this->hasMany(FilamentOrganizations::organizationInvitationModel());
    }

    /**
     * Remove the given user from the organization.
     */
    public function removeUser(mixed $user): void
    {
        if ($user->current_organization_id === $this->id) {
            $user->forceFill([
                'current_organization_id' => null,
            ])->save();
        }

        $this->users()->detach($user);
    }

    /**
     * Purge all the organization's resources.
     */
    public function purge(): void
    {
        $this->owner()->where('current_organization_id', $this->id)
            ->update(['current_organization_id' => null]);

        $this->users()->where('current_organization_id', $this->id)
            ->update(['current_organization_id' => null]);

        $this->users()->detach();

        $this->delete();
    }
}
