<?php

namespace Joinapi\FilamentOrganizations\Http\Livewire;

use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Joinapi\FilamentOrganizations\Contracts\CreatesOrganizations;
use Joinapi\FilamentOrganizations\RedirectsActions;

class CreateOrganizationForm extends Component
{
    use RedirectsActions;

    /**
     * The component's state.
     */
    public array $state = [];

    /**
     * Create a new organization.
     */
    public function createOrganization(CreatesOrganizations $creator): Response | Redirector | RedirectResponse
    {
        $this->resetErrorBag();

        $creator->create($this->user, $this->state);

        $name = $this->state['name'];

        $this->organizationCreated($name);

        return $this->redirectPath($creator);
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
        return view('filament-organizations::organizations.create-organization-form');
    }

    public function organizationCreated($name): void
    {
        Notification::make()
            ->title(__('filament-organizations::default.notifications.organization_created.title'))
            ->success()
            ->body(Str::inlineMarkdown(__('filament-organizations::default.notifications.organization_created.body', compact('name'))))
            ->send();
    }
}
