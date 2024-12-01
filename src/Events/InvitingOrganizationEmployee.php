<?php

namespace Joinapi\FilamentOrganizations\Events;

use Illuminate\Foundation\Events\Dispatchable;

class InvitingOrganizationEmployee
{
    use Dispatchable;

    /**
     * The organization instance.
     */
    public mixed $organization;

    /**
     * The email address of the invitee.
     */
    public string $email;

    /**
     * The role of the invitee.
     */
    public ?string $role = null;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(mixed $organization, string $email, ?string $role = null)
    {
        $this->organization = $organization;
        $this->email = $email;
        $this->role = $role;
    }
}
