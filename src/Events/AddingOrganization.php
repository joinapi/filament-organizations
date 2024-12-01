<?php

namespace Joinapi\FilamentOrganizations\Events;

use Illuminate\Foundation\Events\Dispatchable;

class AddingOrganization
{
    use Dispatchable;

    /**
     * The organization owner.
     */
    public mixed $owner;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(mixed $owner)
    {
        $this->owner = $owner;
    }
}
