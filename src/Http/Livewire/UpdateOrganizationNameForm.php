<?php

namespace Joinapi\FilamentOrganizations\Http\Livewire;

use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Joinapi\FilamentOrganizations\Contracts\UpdatesOrganizationNames;
use Joinapi\FilamentOrganizations\FilamentOrganizations;

class UpdateOrganizationNameForm extends Component
{
    /**
     * The organization instance.
     */
    public mixed $organization;

    /**
     * The component's state.
     */
    public array $state = [];

    /**
     * Mount the component.
     */
    public function mount(mixed $organization): void
    {
        $this->organization = $organization;

        $this->state = $organization->withoutRelations()->toArray();
    }

    /**
     * Update the organization's name.
     */
    public function updateOrganizationName(UpdatesOrganizationNames $updater): void
    {
        $this->resetErrorBag();

        $updater->update($this->user, $this->organization, $this->state);

        if (FilamentOrganizations::hasNotificationsFeature()) {
            if (method_exists($updater, 'organizationNameUpdated')) {
                $updater->organizationNameUpdated($this->user, $this->organization, $this->state);
            } else {
                $this->organizationNameUpdated($this->organization);
            }
        }
    }

    protected function organizationNameUpdated($organization): void
    {
        $name = $organization->name;

        Notification::make()
            ->title(__('filament-organizations::default.notifications.organization_name_updated.title'))
            ->success()
            ->body(Str::inlineMarkdown(__('filament-organizations::default.notifications.organization_name_updated.body', compact('name'))))
            ->send();
    }

    /**
     * Get the current user of the application.
     */
    public function getUserProperty(): ?Authenticatable
    {
        return Auth::user();
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('filament-organizations::organizations.update-organization-name-form');
    }
}
