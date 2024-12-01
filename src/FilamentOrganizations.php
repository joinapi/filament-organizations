<?php

namespace Joinapi\FilamentOrganizations;

use Filament\Contracts\Plugin;
use Filament\Events\TenantSet;
use Filament\Panel;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Joinapi\FilamentOrganizations\Contracts\CreatesConnectedAccounts;
use Joinapi\FilamentOrganizations\Contracts\CreatesUserFromProvider;
use Joinapi\FilamentOrganizations\Contracts\HandlesInvalidState;
use Joinapi\FilamentOrganizations\Contracts\UpdatesConnectedAccounts;
use Joinapi\FilamentOrganizations\Http\Controllers\OAuthController;
use Joinapi\FilamentOrganizations\Listeners\SwitchCurrentOrganization;
use Joinapi\FilamentOrganizations\Pages\Organization\OrganizationSettings;
use Joinapi\FilamentOrganizations\Pages\Organization\CreateOrganization;

class FilamentOrganizations implements Plugin
{
    use Concerns\Base\HasAddedProfileComponents;
    use Concerns\Base\HasBaseActionBindings;
    use Concerns\Base\HasBaseModels;
    use Concerns\Base\HasBaseProfileComponents;
    use Concerns\Base\HasBaseProfileFeatures;
    use Concerns\Base\HasOrganizationFeatures;
    use Concerns\Base\HasModals;
    use Concerns\Base\HasNotifications;
    use Concerns\Base\HasPanels;
    use Concerns\Base\HasPermissions;
    use Concerns\Base\HasRoutes;
    use Concerns\Base\HasTermsAndPrivacyPolicy;
    use Concerns\ManagesProfileComponents;
    use Concerns\Socialite\CanEnableSocialite;
    use Concerns\Socialite\HasConnectedAccountModel;
    use Concerns\Socialite\HasProviderFeatures;
    use Concerns\Socialite\HasProviders;
    use Concerns\Socialite\HasSocialiteActionBindings;
    use Concerns\Socialite\HasSocialiteComponents;
    use Concerns\Socialite\HasSocialiteProfileFeatures;

    public function getId(): string
    {
        return 'organizations';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function register(Panel $panel): void
    {
        if (static::hasOrganizationFeatures()) {
            Livewire::component('filament.pages.organizations.create_organization', CreateOrganization::class);
            Livewire::component('filament.pages.organizations.organization_settings', OrganizationSettings::class);
        }

        if (static::hasSocialiteFeatures()) {
            app()->bind(OAuthController::class, static function (Application $app) {
                return new OAuthController(
                    $app->make(CreatesUserFromProvider::class),
                    $app->make(CreatesConnectedAccounts::class),
                    $app->make(UpdatesConnectedAccounts::class),
                    $app->make(HandlesInvalidState::class),
                );
            });
        }

        if (static::$registersRoutes) {
            $panel->routes(fn () => $this->registerPublicRoutes());
            $panel->authenticatedRoutes(fn () => $this->registerAuthenticatedRoutes());
        }
    }

    public function boot(Panel $panel): void
    {
        if (static::switchesCurrentOrganization()) {
            Event::listen(TenantSet::class, SwitchCurrentOrganization::class);
        }
    }
}
