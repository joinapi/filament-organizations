<?php

namespace Joinapi\FilamentOrganizations\Rules;

use Illuminate\Contracts\Validation\Rule;
use Joinapi\FilamentOrganizations\FilamentOrganizations;

class Role implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        return array_key_exists($value, FilamentOrganizations::$roles);
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return __('filament-organizations::default.errors.valid_role');
    }
}
