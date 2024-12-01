<?php

namespace App\Providers;

use App\Actions\FilamentOrganizations\AddOrganizationEmployee;
use App\Actions\FilamentOrganizations\CreateNewUser;
use App\Actions\FilamentOrganizations\DeleteOrganization;
use App\Actions\FilamentOrganizations\DeleteUser;
use App\Actions\FilamentOrganizations\InviteOrganizationEmployee;
use App\Actions\FilamentOrganizations\RemoveOrganizationEmployee;
use App\Actions\FilamentOrganizations\UpdateOrganizationName;
use App\Actions\FilamentOrganizations\UpdateUserPassword;
use App\Actions\FilamentOrganizations\UpdateUserProfileInformation;
use App\Models\Organization;
use App\Models\Organizations;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Joinapi\FilamentOrganizations\FilamentOrganizations;
use Joinapi\FilamentOrganizations\Pages\Auth\Login;
use Joinapi\FilamentOrganizations\Pages\Auth\Register;
use Joinapi\FilamentOrganizations\Pages\Organization\CreateOrganization;
use Joinapi\FilamentOrganizations\Pages\Organization\OrganizationSettings;
use Joinapi\FilamentOrganizations\Pages\Organizations\OrganizationsSettings;
use Joinapi\FilamentOrganizations\Pages\Organizations\CreateOrganizations;
use Joinapi\FilamentOrganizations\Pages\User\Profile;

class FilamentOrganizationsServiceProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('organization')
            ->path('organization')
            ->default()
            ->login(Login::class)
            ->passwordReset()
            ->homeUrl(static fn (): string => url(Pages\Dashboard::getUrl(panel: 'Organizations', tenant: Auth::user()?->personalOrganizations())))
            ->plugin(
                FilamentOrganizations::make()
                    ->userPanel('party')
                    ->switchCurrentOrganization()
                    ->updateProfileInformation()
                    ->updatePasswords()
                    ->manageBrowserSessions()
                    ->accountDeletion()
                    ->profilePhotos()
                    ->api()
                    ->organizations(invitations: true)
                    ->termsAndPrivacyPolicy()
                    ->notifications()
                    ->modals(),
            )
            ->registration(Register::class)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->tenant(Organization::class)
            ->tenantProfile(OrganizationSettings::class)
            ->tenantRegistration(CreateOrganization::class)
            ->discoverResources(in: app_path('Filament/Organization/Resources'), for: 'App\\Filament\\Organization\\Resources')
            ->discoverPages(in: app_path('Filament/Organization/Pages'), for: 'App\\Filament\\Organizations\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label('Profile')
                    ->icon('heroicon-o-user-circle')
                    ->url(static fn () => route(Profile::getRouteName(panel: 'admin'))),
            ])
            ->authGuard('web')
            ->discoverWidgets(in: app_path('Filament/Organizations/Widgets'), for: 'App\\Filament\\Organizations\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePermissions();

        FilamentOrganizations::createUsersUsing(CreateNewUser::class);
        FilamentOrganizations::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        FilamentOrganizations::updateUserPasswordsUsing(UpdateUserPassword::class);

        FilamentOrganizations::createOrganizationsUsing(CreateOrganization::class);
        FilamentOrganizations::updateOrganizationNamesUsing(UpdateOrganizationName::class);
        FilamentOrganizations::addOrganizationEmployeesUsing(AddOrganizationEmployee::class);
        FilamentOrganizations::inviteOrganizationEmployeesUsing(InviteOrganizationEmployee::class);
        FilamentOrganizations::removeOrganizationEmployeesUsing(RemoveOrganizationEmployee::class);
        FilamentOrganizations::deleteOrganizationsUsing(DeleteOrganization::class);
        FilamentOrganizations::deleteUsersUsing(DeleteUser::class);
    }

    /**
     * Configure the roles and permissions that are available within the application.
     */
    protected function configurePermissions(): void
    {
        FilamentOrganizations::defaultApiTokenPermissions(['read']);

        FilamentOrganizations::role('admin', 'Administrator', [
            'create',
            'read',
            'update',
            'delete',
        ])->description('Administrator users can perform any action.');

        FilamentOrganizations::role('editor', 'Editor', [
            'read',
            'create',
            'update',
        ])->description('Editor users have the ability to read, create, and update.');
    }
}
