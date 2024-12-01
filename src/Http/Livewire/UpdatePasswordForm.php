<?php

namespace Joinapi\FilamentOrganizations\Http\Livewire;

use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Joinapi\FilamentOrganizations\Contracts\UpdatesUserPasswords;
use Joinapi\FilamentOrganizations\FilamentOrganizations;

class UpdatePasswordForm extends Component
{
    /**
     * The component's state.
     *
     * @var array<string, mixed>
     */
    public $state = [
        'current_password' => '',
        'password' => '',
        'password_confirmation' => '',
    ];

    /**
     * Update the user's password.
     */
    public function updatePassword(UpdatesUserPasswords $updater): void
    {
        $this->resetErrorBag();

        $updater->update($this->user, $this->state);

        if (session() !== null) {
            session()->put([
                'password_hash_' . Auth::getDefaultDriver() => $this->user?->getAuthPassword(),
            ]);
        }

        $this->state = [
            'current_password' => '',
            'password' => '',
            'password_confirmation' => '',
        ];

        if (FilamentOrganizations::hasNotificationsFeature()) {
            if (method_exists($updater, 'passwordUpdated')) {
                $updater->passwordUpdated($this->user, $this->state);
            } else {
                $this->passwordUpdated();
            }
        }
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
        return view('filament-organizations::profile.update-password-form');
    }

    public function passwordUpdated(): void
    {
        Notification::make()
            ->title(__('filament-organizations::default.notifications.password_updated.title'))
            ->success()
            ->body(__('filament-organizations::default.notifications.password_updated.body'))
            ->send();
    }
}
