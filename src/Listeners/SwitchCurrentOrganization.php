<?php

namespace Joinapi\FilamentOrganizations\Listeners;

use Filament\Events\TenantSet;
use Joinapi\FilamentOrganizations\FilamentOrganizations;
use Joinapi\FilamentOrganizations\HasOrganizations;

class SwitchCurrentOrganization
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TenantSet $event): void
    {
        $tenant = $event->getTenant();

        /** @var HasOrganizations $user */
        $user = $event->getUser();

        if (FilamentOrganizations::switchesCurrentOrganization() === false || ! in_array(HasOrganizations::class, class_uses_recursive($user), true)) {
            return;
        }

        if (! $user->switchOrganization($tenant)) {
            $user->switchOrganization($user->personalOrganization());
        }
    }
}
