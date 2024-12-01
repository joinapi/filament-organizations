<?php

namespace Joinapi\FilamentOrganizations\Http\Livewire;

use Filament\Notifications\Notification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Joinapi\FilamentOrganizations\Actions\ValidateOrganizationDeletion;
use Joinapi\FilamentOrganizations\Contracts\DeletesOrganizations;
use Joinapi\FilamentOrganizations\FilamentOrganizations;
use Joinapi\FilamentOrganizations\RedirectsActions;

class DeleteOrganizationForm extends Component
{
    use RedirectsActions;

    /**
     * The organization instance.
     */
    public mixed $organization;

    /**
     * Mount the component.
     */
    public function mount(mixed $organization): void
    {
        $this->organization = $organization;
    }

    /**
     * Delete the organization.
     *
     * @throws AuthorizationException
     */
    public function deleteOrganization(ValidateOrganizationDeletion $validator, DeletesOrganizations $deleter): Response | Redirector | RedirectResponse
    {
        $validator->validate(Auth::user(), $this->organization);

        $deleter->delete($this->organization);

        if (FilamentOrganizations::hasNotificationsFeature()) {
            if (method_exists($deleter, 'organizationDeleted')) {
                $deleter->organizationDeleted($this->organization);
            } else {
                $this->organizationDeleted($this->organization);
            }
        }

        $this->organization = null;

        return $this->redirectPath($deleter);
    }

    /**
     * Cancel the organization deletion.
     */
    public function cancelOrganizationDeletion(): void
    {
        $this->dispatch('close-modal', id: 'confirmingOrganizationDeletion');
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('filament-organizations::organizations.delete-organization-form');
    }

    public function organizationDeleted($organization): void
    {
        $name = $organization->name;

        Notification::make()
            ->title(__('filament-organizations::default.notifications.organization_deleted.title'))
            ->success()
            ->body(Str::inlineMarkdown(__('filament-organizations::default.notifications.organization_deleted.body', compact('name'))))
            ->send();
    }
}
