<?php

namespace App\Actions\FilamentOrganizations;

use App\Models\Organization;
use Joinapi\FilamentOrganizations\Contracts\DeletesOrganizations;

class DeleteOrganization implements DeletesOrganizations
{
    /**
     * Delete the given organization.
     */
    public function delete(Organization $organization): void
    {
        $organization->purge();
    }
}
