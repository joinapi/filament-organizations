<?php

namespace App\Actions\FilamentOrganizations;

use App\Models\Organization;
use App\Models\User;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Joinapi\FilamentOrganizations\Contracts\InvitesOrganizationEmployees;
use Joinapi\FilamentOrganizations\Events\InvitingOrganizationEmployee;
use Joinapi\FilamentOrganizations\FilamentOrganizations;
use Joinapi\FilamentOrganizations\Mail\OrganizationInvitation;
use Joinapi\FilamentOrganizations\Rules\Role;

class InviteOrganizationEmployee implements InvitesOrganizationEmployees
{
    /**
     * Invite a new organization employee to the given organization.
     *
     * @throws AuthorizationException
     */
    public function invite(User $user, Organization $organization, string $email, ?string $role = null): void
    {
        Gate::forUser($user)->authorize('addOrganizationEmployee', $organization);

        $this->validate($organization, $email, $role);

        InvitingOrganizationEmployee::dispatch($organization, $email, $role);

        $invitation = $organization->organizationInvitations()->create([
            'email' => $email,
            'role' => $role,
        ]);

        Mail::to($email)->send(new OrganizationInvitation($invitation));
    }

    /**
     * Validate the invite employee operation.
     */
    protected function validate(Organization $organization, string $email, ?string $role): void
    {
        Validator::make([
            'email' => $email,
            'role' => $role,
        ], $this->rules($organization), [
            'email.unique' => __('filament-organizations::default.errors.employee_already_invited'),
        ])->after(
            $this->ensureUserIsNotAlreadyOnOrganization($organization, $email)
        )->validateWithBag('addOrganizationEmployee');
    }

    /**
     * Get the validation rules for inviting a organization employee.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    protected function rules(Organization $organization): array
    {
        return array_filter([
            'email' => [
                'required', 'email',
                Rule::unique('organization_invitations')->where(static function (Builder $query) use ($organization) {
                    $query->where('organization_id', $organization->id);
                }),
            ],
            'role' => FilamentOrganizations::hasRoles()
                            ? ['required', 'string', new Role]
                            : null,
        ]);
    }

    /**
     * Ensure that the employee is not already on the organization.
     */
    protected function ensureUserIsNotAlreadyOnOrganization(Organization $organization, string $email): Closure
    {
        return static function ($validator) use ($organization, $email) {
            $validator->errors()->addIf(
                $organization->hasUserWithEmail($email),
                'email',
                __('filament-organizations::default.errors.employee_already_belongs_to_organization')
            );
        };
    }
}
