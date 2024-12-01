<?php

namespace App\Actions\FilamentOrganizations;

use App\Models\Organization;
use App\Models\User;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Joinapi\FilamentOrganizations\Contracts\AddsOrganizationEmployees;
use Joinapi\FilamentOrganizations\Events\AddingOrganizationEmployee;
use Joinapi\FilamentOrganizations\Events\OrganizationEmployeeAdded;
use Joinapi\FilamentOrganizations\FilamentOrganizations;
use Joinapi\FilamentOrganizations\Rules\Role;

class AddOrganizationEmployee implements AddsOrganizationEmployees
{
    /**
     * Add a new organization employee to the given organization.
     *
     * @throws AuthorizationException
     */
    public function add(User $user, Organization $organization, string $email, ?string $role = null): void
    {
        Gate::forUser($user)->authorize('addOrganizationEmployee', $organization);

        $this->validate($organization, $email, $role);

        $newOrganizationEmployee = FilamentOrganizations::findUserByEmailOrFail($email);

        AddingOrganizationEmployee::dispatch($organization, $newOrganizationEmployee);

        $organization->users()->attach(
            $newOrganizationEmployee,
            ['role' => $role]
        );

        OrganizationEmployeeAdded::dispatch($organization, $newOrganizationEmployee);
    }

    /**
     * Validate the add employee operation.
     */
    protected function validate(Organization $organization, string $email, ?string $role): void
    {
        Validator::make([
            'email' => $email,
            'role' => $role,
        ], $this->rules(), [
            'email.exists' => __('filament-organizations::default.errors.email_not_found'),
        ])->after(
            $this->ensureUserIsNotAlreadyOnOrganization($organization, $email)
        )->validateWithBag('addOrganizationEmployee');
    }

    /**
     * Get the validation rules for adding a organization employee.
     *
     * @return array<string, Rule|array|string>
     */
    protected function rules(): array
    {
        return array_filter([
            'email' => ['required', 'email', 'exists:users'],
            'role' => FilamentOrganizations::hasRoles()
                            ? ['required', 'string', new Role]
                            : null,
        ]);
    }

    /**
     * Ensure that the user is not already on the organization.
     */
    protected function ensureUserIsNotAlreadyOnOrganization(Organization $organization, string $email): Closure
    {
        return static function ($validator) use ($organization, $email) {
            $validator->errors()->addIf(
                $organization->hasUserWithEmail($email),
                'email',
                __('filament-organizations::default.errors.user_belongs_to_organization')
            );
        };
    }
}
