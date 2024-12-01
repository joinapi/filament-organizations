<?php

namespace Joinapi\FilamentOrganizations;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

trait HasOrganizations
{
    /**
     * Determine if the given organization is the current organization.
     */
    public function isCurrentOrganization(mixed $organization): bool
    {
        return $organization->id === $this->currentOrganization->id;
    }

    /**
     * Get the current organization of the user's filament-organizations.
     */
    public function currentOrganization(): BelongsTo
    {
        if ($this->current_organization_id === null && $this->id) {
            $this->switchOrganization($this->personalOrganization());
        }

        return $this->belongsTo(FilamentOrganizations::organizationModel(), 'current_organization_id');
    }

    /**
     * Switch the user's filament-organizations to the given organization.
     */
    public function switchOrganization(mixed $organization): bool
    {
        if (! $this->belongsToOrganization($organization)) {
            return false;
        }

        $this->forceFill([
            'current_organization_id' => $organization->id,
        ])->save();

        $this->setRelation('currentOrganization', $organization);

        return true;
    }

    /**
     * Get all the organizations the user owns or belongs to.
     */
    public function allOrganizations(): Collection
    {
        return $this->ownedOrganizations->merge($this->organizations)->sortBy('name');
    }

    /**
     * Get all the organizations the user owns.
     */
    public function ownedOrganizations(): HasMany
    {
        return $this->hasMany(FilamentOrganizations::organizationModel());
    }

    /**
     * Get all the organizations the user belongs to.
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(FilamentOrganizations::organizationModel(), FilamentOrganizations::employeeshipModel())
            ->withPivot('role')
            ->withTimestamps()
            ->as('employeeship');
    }

    /**
     * Get the user's "personal" organization.
     */
    public function personalOrganization(): mixed
    {
        return $this->ownedOrganizations->where('personal_organization', true)->first();
    }

    /**
     * Determine if the user owns the given organization.
     */
    public function ownsOrganization(mixed $organization): bool
    {
        if ($organization === null) {
            return false;
        }

        return $this->id === $organization->{$this->getForeignKey()};
    }

    /**
     * Determine if the user belongs to the given organization.
     */
    public function belongsToOrganization(mixed $organization): bool
    {
        if ($organization === null) {
            return false;
        }

        return $this->ownsOrganization($organization) || $this->organizations->contains(static function ($t) use ($organization) {
            return $t->id === $organization->id;
        });
    }

    /**
     * Get the role that the user has on the organization.
     */
    public function organizationRole(mixed $organization): ?Role
    {
        if ($this->ownsOrganization($organization)) {
            return new OwnerRole;
        }

        if (! $this->belongsToOrganization($organization)) {
            return null;
        }

        $role = $organization->users
            ->where('id', $this->id)
            ->first()
            ->employeeship
            ->role;

        return $role ? FilamentOrganizations::findRole($role) : null;
    }

    /**
     * Determine if the user has the given role on the given organization.
     */
    public function hasOrganizationRole(mixed $organization, string $role): bool
    {
        if ($this->ownsOrganization($organization)) {
            return true;
        }

        return $this->belongsToOrganization($organization) && FilamentOrganizations::findRole($organization->users->where(
            'id',
            $this->id
        )->first()->employeeship->role)?->key === $role;
    }

    /**
     * Get the user's permissions for the given organization.
     */
    public function organizationPermissions(mixed $organization): array
    {
        if ($this->ownsOrganization($organization)) {
            return ['*'];
        }

        if (! $this->belongsToOrganization($organization)) {
            return [];
        }

        return (array) $this->organizationRole($organization)?->permissions;
    }

    /**
     * Determine if the user has the given permission on the given organization.
     */
    public function hasOrganizationPermission(mixed $organization, string $permission): bool
    {
        if ($this->ownsOrganization($organization)) {
            return true;
        }

        if (! $this->belongsToOrganization($organization)) {
            return false;
        }

        if ($this->currentAccessToken() !== null &&
            ! $this->tokenCan($permission) &&
            in_array(HasApiTokens::class, class_uses_recursive($this), true)) {
            return false;
        }

        $permissions = $this->organizationPermissions($organization);

        return in_array($permission, $permissions, true) ||
            in_array('*', $permissions, true) ||
            (Str::endsWith($permission, ':create') && in_array('*:create', $permissions, true)) ||
            (Str::endsWith($permission, ':update') && in_array('*:update', $permissions, true));
    }
}
