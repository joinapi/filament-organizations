<?php

namespace App\Actions\FilamentOrganizations;

use Illuminate\Http\Response;
use Laravel\Socialite\Two\InvalidStateException;
use Joinapi\FilamentOrganizations\Contracts\HandlesInvalidState;

class HandleInvalidState implements HandlesInvalidState
{
    /**
     * Handle an invalid state exception from a Socialite provider.
     */
    public function handle(InvalidStateException $exception, ?callable $callback = null): Response
    {
        if ($callback) {
            return $callback($exception);
        }

        throw $exception;
    }
}
