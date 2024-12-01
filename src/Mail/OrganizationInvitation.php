<?php

namespace Joinapi\FilamentOrganizations\Mail;

use App\Models\OrganizationInvitation as OrganizationInvitationModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Joinapi\FilamentOrganizations\FilamentOrganizations;

class OrganizationInvitation extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * The organization invitation instance.
     */
    public OrganizationInvitationModel $invitation;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(OrganizationInvitationModel $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        $acceptUrl = FilamentOrganizations::generateAcceptInvitationUrl($this->invitation);

        return $this->markdown('filament-organizations::mail.organization-invitation', compact('acceptUrl'))
            ->subject(__('Organization Invitation'));
    }
}
