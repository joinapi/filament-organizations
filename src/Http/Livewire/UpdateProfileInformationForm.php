<?php

namespace Joinapi\FilamentOrganizations\Http\Livewire;

use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Joinapi\FilamentOrganizations\Contracts\UpdatesUserProfileInformation;
use Joinapi\FilamentOrganizations\FilamentOrganizations;
use Joinapi\FilamentOrganizations\Pages\User\Profile;
use Livewire\Component;
use Livewire\WithFileUploads;

class UpdateProfileInformationForm extends Component
{
    use WithFileUploads;

    /**
     * The component's state.
     */
    public array $state = [];

    /**
     * The new avatar for the user.
     */
    public $photo;

    /**
     * Determine if the verification email was sent.
     */
    public bool $verificationLinkSent = false;

    /**
     * Prepare the component.
     */
    public function mount(): void
    {
        $user = $this->user;

        $this->state = ['email' => $user?->email, ...$user?->withoutRelations()->toArray()];
    }

    /**
     * Update the user's profile information.
     */
    public function updateProfileInformation(UpdatesUserProfileInformation $updater): void
    {
        $this->resetErrorBag();

        $updater->update(
            $this->user,
            $this->photo
                ? [...$this->state, 'photo' => $this->photo]
                : $this->state
        );

        if (isset($this->photo)) {
            redirect(Profile::getUrl());
        }

        if (FilamentOrganizations::hasNotificationsFeature()) {
            if (method_exists($updater, 'profileInformationUpdated')) {
                $updater->profileInformationUpdated($this->user, $this->state);
            } else {
                $this->profileInformationUpdated();
            }
        }
    }

    protected function profileInformationUpdated(): void
    {
        Notification::make()
            ->title(__('filament-organizations::default.notifications.profile_information_updated.title'))
            ->success()
            ->body(__('filament-organizations::default.notifications.profile_information_updated.body'))
            ->send();
    }

    /**
     * Delete user's profile photo.
     */
    public function deleteProfilePhoto(): void
    {
        $this->user?->deleteProfilePhoto();
    }

    /**
     * Sent the email verification.
     */
    public function sendEmailVerification(): void
    {
        $this->user?->sendEmailVerificationNotification();

        $this->verificationLinkSent = true;

        Notification::make()
            ->title(__('filament-organizations::default.notifications.verification_link_sent.title'))
            ->success()
            ->body(__('filament-organizations::default.notifications.verification_link_sent.body'))
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
        return view('filament-organizations::profile.update-profile-information-form');
    }
}
