<?php

namespace Joinapi\FilamentOrganizations\Concerns\Base;

use Closure;
use Joinapi\FilamentOrganizations\HasOrganizations;

trait HasOrganizationFeatures
{
    /**
     * The event listener to register.
     */
    protected static bool $switchesCurrentOrganization = false;

    /**
     * Determine if the organization is supporting organization features.
     */
    public static bool $hasOrganizationFeatures = false;

    /**
     * Determine if invitations are sent to organization employees.
     */
    public static bool $sendsOrganizationInvitations = false;

    /**
     * Determine if the application supports switching current organization.
     */
    public function switchCurrentOrganization(bool $condition = true): static
    {
        static::$switchesCurrentOrganization = $condition;

        return $this;
    }

    /**
     * Determine if the organization is supporting organization features.
     */
    public function organizations(bool | Closure | null $condition = true, bool $invitations = false): static
    {
        static::$hasOrganizationFeatures = $condition instanceof Closure ? $condition() : $condition;
        static::$sendsOrganizationInvitations = $invitations;

        return $this;
    }

    /**
     * Determine if the application switches the current organization.
     */
    public static function switchesCurrentOrganization(): bool
    {
        return static::$switchesCurrentOrganization;
    }

    /**
     * Determine if Organization is supporting organization features.
     */
    public static function hasOrganizationFeatures(): bool
    {
        return static::$hasOrganizationFeatures;
    }

    /**
     * Determine if invitations are sent to organization employees.
     */
    public static function sendsOrganizationInvitations(): bool
    {
        return static::hasOrganizationFeatures() && static::$sendsOrganizationInvitations;
    }

    /**
     * Determine if a given user model utilizes the "HasOrganizations" trait.
     */
    public static function userHasOrganizationFeatures(mixed $user): bool
    {
        return (array_key_exists(HasOrganizations::class, class_uses_recursive($user)) ||
                method_exists($user, 'currentOrganization')) &&
            static::hasOrganizationFeatures();
    }
}
