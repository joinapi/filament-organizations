<?php

namespace Joinapi\FilamentOrganizations\Concerns\Base;

use Joinapi\FilamentOrganizations\Contracts\AddsOrganizationEmployees;
use Joinapi\FilamentOrganizations\Contracts\CreatesOrganizations;
use Joinapi\FilamentOrganizations\Contracts\CreatesNewUsers;
use Joinapi\FilamentOrganizations\Contracts\DeletesOrganizations;
use Joinapi\FilamentOrganizations\Contracts\DeletesUsers;
use Joinapi\FilamentOrganizations\Contracts\InvitesOrganizationEmployees;
use Joinapi\FilamentOrganizations\Contracts\RemovesOrganizationEmployees;
use Joinapi\FilamentOrganizations\Contracts\UpdatesOrganizationNames;
use Joinapi\FilamentOrganizations\Contracts\UpdatesUserPasswords;
use Joinapi\FilamentOrganizations\Contracts\UpdatesUserProfileInformation;

trait HasBaseActionBindings
{
    /**
     * Register a class / callback that should be used to create new users.
     */
    public static function createUsersUsing(string $class): void
    {
        app()->singleton(CreatesNewUsers::class, $class);
    }

    /**
     * Register a class / callback that should be used to update user profile information.
     */
    public static function updateUserProfileInformationUsing(string $class): void
    {
        app()->singleton(UpdatesUserProfileInformation::class, $class);
    }

    /**
     * Register a class / callback that should be used to update user passwords.
     */
    public static function updateUserPasswordsUsing(string $class): void
    {
        app()->singleton(UpdatesUserPasswords::class, $class);
    }

    /**
     * Register a class / callback that should be used to create organizations.
     */
    public static function createOrganizationsUsing(string $class): void
    {
        app()->singleton(CreatesOrganizations::class, $class);
    }

    /**
     * Register a class / callback that should be used to update organization names.
     */
    public static function updateOrganizationNamesUsing(string $class): void
    {
        app()->singleton(UpdatesOrganizationNames::class, $class);
    }

    /**
     * Register a class / callback that should be used to add organization employees.
     */
    public static function addOrganizationEmployeesUsing(string $class): void
    {
        app()->singleton(AddsOrganizationEmployees::class, $class);
    }

    /**
     * Register a class / callback that should be used to add organization employees.
     */
    public static function inviteOrganizationEmployeesUsing(string $class): void
    {
        app()->singleton(InvitesOrganizationEmployees::class, $class);
    }

    /**
     * Register a class / callback that should be used to remove organization employees.
     */
    public static function removeOrganizationEmployeesUsing(string $class): void
    {
        app()->singleton(RemovesOrganizationEmployees::class, $class);
    }

    /**
     * Register a class / callback that should be used to delete organizations.
     */
    public static function deleteOrganizationsUsing(string $class): void
    {
        app()->singleton(DeletesOrganizations::class, $class);
    }

    /**
     * Register a class / callback that should be used to delete users.
     */
    public static function deleteUsersUsing(string $class): void
    {
        app()->singleton(DeletesUsers::class, $class);
    }
}
