<?php

namespace Joinapi\FilamentOrganizations\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class OrganizationEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * The organization instance.
     */
    public mixed $organization;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(mixed $organization)
    {
        $this->organization = $organization;
    }
}
