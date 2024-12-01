<?php

namespace Joinapi\FilamentOrganizations\Events;

use Illuminate\Foundation\Events\Dispatchable;

class OrganizationEmployeeRemoved
{
    use Dispatchable;

    /**
     * The organization instance.
     */
    public mixed $organization;

    /**
     * The organization employee that was removed.
     */
    public mixed $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(mixed $organization, mixed $user)
    {
        $this->organization = $organization;
        $this->user = $user;
    }
}
