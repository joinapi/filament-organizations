<?php

namespace Joinapi\FilamentOrganizations\Pages\Organization;

use Filament\Facades\Filament;
use Filament\Pages\Tenancy\EditTenantProfile as BaseEditTenantProfile;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;

use function Filament\authorize;

class OrganizationSettings extends BaseEditTenantProfile
{
    protected static string $view = 'filament-organizations::filament.pages.organizations.organization_settings';

    public static function getLabel(): string
    {
        return __('filament-organizations::default.pages.titles.organization_settings');
    }

    public static function canView(Model $tenant): bool
    {
        try {
            return authorize('view', $tenant)->allowed();
        } catch (AuthorizationException $exception) {
            return $exception->toResponse()->allowed();
        }
    }

    protected function getViewData(): array
    {
        return [
            'organization' => Filament::getTenant(),
        ];
    }
}
