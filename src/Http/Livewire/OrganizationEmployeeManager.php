<?php

namespace Joinapi\FilamentOrganizations\Http\Livewire;

use Filament\Notifications\Notification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Joinapi\FilamentOrganizations\Actions\UpdateOrganizationEmployeeRole;
use Joinapi\FilamentOrganizations\Contracts\AddsOrganizationEmployees;
use Joinapi\FilamentOrganizations\Contracts\InvitesOrganizationEmployees;
use Joinapi\FilamentOrganizations\Contracts\RemovesOrganizationEmployees;
use Joinapi\FilamentOrganizations\FilamentOrganizations;
use Joinapi\FilamentOrganizations\RedirectsActions;
use Joinapi\FilamentOrganizations\Role;

class OrganizationEmployeeManager extends Component
{
    use RedirectsActions;

    /**
     * The organization instance.
     */
    public mixed $organization;

    /**
     * The user that is having their role managed.
     */
    public mixed $managingRoleFor;

    /**
     * The current role for the user that is having their role managed.
     */
    public string $currentRole;

    /**
     * The ID of the organization employee being removed.
     */
    public ?int $organizationEmployeeIdBeingRemoved = null;

    /**
     * The "add organization employee" form state.
     *
     * @var array<string, mixed>
     */
    public $addOrganizationEmployeeForm = [
        'email' => '',
        'role' => null,
    ];

    /**
     * Mount the component.
     */
    public function mount(mixed $organization): void
    {
        $this->organization = $organization;
    }

    /**
     * Add a new organization employee to a organization.
     */
    public function addOrganizationEmployee(InvitesOrganizationEmployees $inviter, AddsOrganizationEmployees $adder): void
    {
        $this->resetErrorBag();

        if (FilamentOrganizations::sendsOrganizationInvitations()) {
            $inviter->invite(
                $this->user,
                $this->organization,
                $this->addOrganizationEmployeeForm['email'],
                $this->addOrganizationEmployeeForm['role']
            );
        } else {
            $adder->add(
                $this->user,
                $this->organization,
                $this->addOrganizationEmployeeForm['email'],
                $this->addOrganizationEmployeeForm['role']
            );
        }

        if (FilamentOrganizations::hasNotificationsFeature()) {
            if (method_exists($inviter, 'employeeInvitationSent')) {
                $inviter->employeeInvitationSent(
                    $this->user,
                    $this->organization,
                    $this->addOrganizationEmployeeForm['email'],
                    $this->addOrganizationEmployeeForm['role']
                );
            } else {
                $email = $this->addOrganizationEmployeeForm['email'];
                $this->employeeInvitationSent($email);
            }
        }

        $this->addOrganizationEmployeeForm = [
            'email' => '',
            'role' => null,
        ];

        $this->organization = $this->organization->fresh();
    }

    /**
     * Cancel a pending organization employee invitation.
     */
    public function cancelOrganizationInvitation(int $invitationId): void
    {
        if (! empty($invitationId)) {
            $model = FilamentOrganizations::organizationInvitationModel();

            $model::whereKey($invitationId)->delete();
        }

        $this->organization = $this->organization->fresh();
    }

    /**
     * Allow the given user's role to be managed.
     */
    public function manageRole(int $userId): void
    {
        $this->dispatch('open-modal', id: 'currentlyManagingRole');
        $this->managingRoleFor = FilamentOrganizations::findUserByIdOrFail($userId);
        $this->currentRole = $this->managingRoleFor->organizationRole($this->organization)->key;
    }

    /**
     * Save the role for the user being managed.
     *
     * @throws AuthorizationException
     */
    public function updateRole(UpdateOrganizationEmployeeRole $updater): void
    {
        $updater->update(
            $this->user,
            $this->organization,
            $this->managingRoleFor->id,
            $this->currentRole
        );

        $this->organization = $this->organization->fresh();

        $this->dispatch('close-modal', id: 'currentlyManagingRole');
    }

    /**
     * Stop managing the role of a given user.
     */
    public function stopManagingRole(): void
    {
        $this->dispatch('close-modal', id: 'currentlyManagingRole');
    }

    /**
     * Confirm that the currently authenticated user should leave the organization.
     */
    public function confirmLeavingOrganization(): void
    {
        $this->dispatch('open-modal', id: 'confirmingLeavingOrganization');
    }

    /**
     * Remove the currently authenticated user from the organization.
     */
    public function leaveOrganization(RemovesOrganizationEmployees $remover): Response | Redirector | RedirectResponse
    {
        $remover->remove(
            $this->user,
            $this->organization,
            $this->user
        );

        $this->dispatch('close-modal', id: 'confirmingLeavingOrganization');

        $this->organization = $this->organization->fresh();

        return $this->redirectPath($remover);
    }

    /**
     * Cancel leaving the organization.
     */
    public function cancelLeavingOrganization(): void
    {
        $this->dispatch('close-modal', id: 'confirmingLeavingOrganization');
    }

    /**
     * Confirm that the given organization employee should be removed.
     */
    public function confirmOrganizationEmployeeRemoval(int $userId): void
    {
        $this->dispatch('open-modal', id: 'confirmingOrganizationEmployeeRemoval');
        $this->organizationEmployeeIdBeingRemoved = $userId;
    }

    /**
     * Remove a organization employee from the organization.
     */
    public function removeOrganizationEmployee(RemovesOrganizationEmployees $remover): void
    {
        $remover->remove(
            $this->user,
            $this->organization,
            $user = FilamentOrganizations::findUserByIdOrFail($this->organizationEmployeeIdBeingRemoved)
        );

        $this->dispatch('close-modal', id: 'confirmingOrganizationEmployeeRemoval');

        $this->organizationEmployeeIdBeingRemoved = null;

        $this->organization = $this->organization->fresh();
    }

    /**
     * Cancel the removal of a organization employee.
     */
    public function cancelOrganizationEmployeeRemoval(): void
    {
        $this->dispatch('close-modal', id: 'confirmingOrganizationEmployeeRemoval');
    }

    /**
     * Get the current user of the application.
     */
    public function getUserProperty(): ?Authenticatable
    {
        return Auth::user();
    }

    /**
     * Get the available organization employee roles.
     */
    public function getRolesProperty(): array
    {
        return collect(FilamentOrganizations::$roles)->transform(static function ($role) {
            return with($role->jsonSerialize(), static function ($data) {
                return (new Role(
                    $data['key'],
                    $data['name'],
                    $data['permissions']
                ))->description($data['description']);
            });
        })->values()->all();
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('filament-organizations::organizations.organization-employee-manager');
    }

    public function employeeInvitationSent($email): void
    {
        Notification::make()
            ->title(__('filament-organizations::default.notifications.organization_invitation_sent.title'))
            ->success()
            ->body(Str::inlineMarkdown(__('filament-organizations::default.notifications.organization_invitation_sent.body', compact('email'))))
            ->send();
    }
}
