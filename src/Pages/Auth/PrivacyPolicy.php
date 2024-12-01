<?php

namespace Joinapi\FilamentOrganizations\Pages\Auth;

use Filament\Pages\Concerns\HasRoutes;
use Filament\Pages\SimplePage;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;
use Joinapi\FilamentOrganizations\FilamentOrganizations;

class PrivacyPolicy extends SimplePage
{
    use HasRoutes;

    protected static string $view = 'filament-organizations::auth.policy';

    protected function getViewData(): array
    {
        $policyFile = FilamentOrganizations::localizedMarkdownPath('policy.md');

        return [
            'policy' => Str::markdown(file_get_contents($policyFile)),
        ];
    }

    public function getHeading(): string | Htmlable
    {
        return '';
    }

    public function getMaxWidth(): MaxWidth | string | null
    {
        return MaxWidth::TwoExtraLarge;
    }

    public static function getSlug(): string
    {
        return static::$slug ?? 'privacy-policy';
    }

    public static function getRouteName(): string
    {
        return 'auth.policy';
    }
}
