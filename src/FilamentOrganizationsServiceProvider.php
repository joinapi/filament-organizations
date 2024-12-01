<?php

namespace Joinapi\FilamentOrganizations;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Joinapi\FilamentOrganizations\Http\Livewire\OrganizationEmployeeManager;
use Joinapi\FilamentOrganizations\Http\Livewire\ConnectedAccountsForm;
use Joinapi\FilamentOrganizations\Http\Livewire\DeleteOrganizationForm;
use Joinapi\FilamentOrganizations\Http\Livewire\DeleteUserForm;
use Joinapi\FilamentOrganizations\Http\Livewire\LogoutOtherBrowserSessionsForm;
use Joinapi\FilamentOrganizations\Http\Livewire\SetPasswordForm;
use Joinapi\FilamentOrganizations\Http\Livewire\UpdateOrganizationNameForm;
use Joinapi\FilamentOrganizations\Http\Livewire\UpdatePasswordForm;
use Joinapi\FilamentOrganizations\Http\Livewire\UpdateProfileInformationForm;

class FilamentOrganizationsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament-organizations');

        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'filament-organizations');

        $this->configurePublishing();
        $this->configureCommands();

        $this->app->booted(function () {
            $this->configureComponents();
        });
    }

    /**
     * Configure the components offered by the application.
     */
    protected function configureComponents(): void
    {
        $featureComponentMap = [
            'update-profile-information-form' => [FilamentOrganizations::canUpdateProfileInformation(), UpdateProfileInformationForm::class],
            'update-password-form' => [FilamentOrganizations::canUpdatePasswords(), UpdatePasswordForm::class],
            'delete-user-form' => [FilamentOrganizations::hasAccountDeletionFeatures(), DeleteUserForm::class],
            'logout-other-browser-sessions-form' => [FilamentOrganizations::canManageBrowserSessions(), LogoutOtherBrowserSessionsForm::class],
            'update-organization-name-form' => [FilamentOrganizations::hasOrganizationFeatures(), UpdateOrganizationNameForm::class],
            'organization-employee-manager' => [FilamentOrganizations::hasOrganizationFeatures(), OrganizationEmployeeManager::class],
            'delete-organization-form' => [FilamentOrganizations::hasOrganizationFeatures(), DeleteOrganizationForm::class],
            'set-password-form' => [FilamentOrganizations::canSetPasswords(), SetPasswordForm::class],
            'connected-accounts-form' => [FilamentOrganizations::canManageConnectedAccounts(), ConnectedAccountsForm::class],
        ];

        foreach ($featureComponentMap as $alias => [$enabled, $component]) {
            if ($enabled) {
                Livewire::component($alias, $component);
            }
        }
    }

    /**
     * Configure publishing for the package.
     */
    protected function configurePublishing(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/filament-organizations'),
        ], 'filament-organizations-views');

        $this->publishes([
            __DIR__ . '/../lang' => lang_path('vendor/filament-organizations'),
        ], 'filament-organizations-translations');

        $this->publishes([
            __DIR__ . '/../database/migrations/0001_01_01_000000_create_users_table.php' => database_path('migrations/0001_01_01_000000_create_users_table.php'),
        ], 'filament-organizations-migrations');

        $this->publishesMigrations([
            __DIR__ . '/../database/migrations/2020_05_21_100000_create_organizations_table.php' => database_path('migrations/2020_05_21_100000_create_organizations_table.php'),
            __DIR__ . '/../database/migrations/2020_05_21_200000_create_organization_user_table.php' => database_path('migrations/2020_05_21_200000_create_organization_user_table.php'),
            __DIR__ . '/../database/migrations/2020_05_21_300000_create_organization_invitations_table.php' => database_path('migrations/2020_05_21_300000_create_organization_invitations_table.php'),
        ], 'filament-organizations-organization-migrations');

        $this->publishesMigrations([
            __DIR__ . '/../database/migrations/2020_12_22_000000_create_connected_accounts_table.php' => database_path('migrations/2020_12_22_000000_create_connected_accounts_table.php'),
        ], 'filament-organizations-socialite-migrations');
    }

    /**
     * Configure the commands offered by the application.
     */
    protected function configureCommands(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            Console\InstallCommand::class,
        ]);
    }
}
