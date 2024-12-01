<?php

namespace Joinapi\FilamentOrganizations\Concerns\Base;

use App\Models\Organization;
use App\Models\OrganizationInvitation;
use App\Models\Employeeship;
use App\Models\User;

trait HasBaseModels
{
    /**
     * The user model that should be used by Organization.
     */
    public static string $userModel = User::class;

    /**
     * The organization model that should be used by Organization.
     */
    public static string $organizationModel = Organization::class;

    /**
     * The employeeship model that should be used by Organization.
     */
    public static string $employeeshipModel = Employeeship::class;

    /**
     * The organization invitation model that should be used by Organization.
     */
    public static string $organizationInvitationModel = OrganizationInvitation::class;

    /**
     * Get the name of the user model used by the application.
     */
    public static function userModel(): string
    {
        return static::$userModel;
    }

    /**
     * Get the name of the organization model used by the application.
     */
    public static function organizationModel(): string
    {
        return static::$organizationModel;
    }

    /**
     * Get the name of the employeeship model used by the application.
     */
    public static function employeeshipModel(): string
    {
        return static::$employeeshipModel;
    }

    /**
     * Get the name of the organization invitation model used by the application.
     */
    public static function organizationInvitationModel(): string
    {
        return static::$organizationInvitationModel;
    }

    /**
     * Get a new instance of the user model.
     */
    public static function newUserModel(): mixed
    {
        $model = static::userModel();

        return new $model;
    }

    /**
     * Get a new instance of the organization model.
     */
    public static function newOrganizationModel(): mixed
    {
        $model = static::organizationModel();

        return new $model;
    }

    /**
     * Specify the user model that should be used by Organization.
     */
    public static function useUserModel(string $model): static
    {
        static::$userModel = $model;

        return new static;
    }

    /**
     * Specify the organization model that should be used by Organization.
     */
    public static function useOrganizationModel(string $model): static
    {
        static::$organizationModel = $model;

        return new static;
    }

    /**
     * Specify the employeeship model that should be used by Organization.
     */
    public static function useEmployeeshipModel(string $model): static
    {
        static::$employeeshipModel = $model;

        return new static;
    }

    /**
     * Specify the organization invitation model that should be used by Organization.
     */
    public static function useOrganizationInvitationModel(string $model): static
    {
        static::$organizationInvitationModel = $model;

        return new static;
    }

    /**
     * Find a user instance by the given ID.
     */
    public static function findUserByIdOrFail(int $id): mixed
    {
        return static::newUserModel()->where('id', $id)->firstOrFail();
    }

    /**
     * Find a user instance by the given email address or fail.
     */
    public static function findUserByEmailOrFail(string $email): mixed
    {
        return static::newUserModel()->where('email', $email)->firstOrFail();
    }
}
