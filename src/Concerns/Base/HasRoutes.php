<?php

namespace Joinapi\FilamentOrganizations\Concerns\Base;

use App\Models\OrganizationInvitation;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Joinapi\FilamentOrganizations\Http\Controllers\OrganizationInvitationController;
use Joinapi\FilamentOrganizations\Http\Controllers\OAuthController;
use Joinapi\FilamentOrganizations\Pages\Auth\PrivacyPolicy;
use Joinapi\FilamentOrganizations\Pages\Auth\Terms;

trait HasRoutes
{
    /**
     * Indicates if Organization routes will be registered.
     */
    public static bool $registersRoutes = true;

    /**
     * Configure Organization to not register its routes.
     */
    public function ignoreRoutes(): static
    {
        static::$registersRoutes = false;

        return $this;
    }

    protected function registerPublicRoutes(): void
    {
        if (static::hasSocialiteFeatures()) {
            Route::get('/oauth/{provider}', [OAuthController::class, 'redirectToProvider'])->name('oauth.redirect');
            Route::get('/oauth/{provider}/callback', [OAuthController::class, 'handleProviderCallback'])->name('oauth.callback');
        }

        if (static::hasTermsAndPrivacyPolicyFeature()) {
            Route::get(Terms::getSlug(), Terms::class)->name(Terms::getRouteName());
            Route::get(PrivacyPolicy::getSlug(), PrivacyPolicy::class)->name(PrivacyPolicy::getRouteName());
        }
    }

    protected function registerAuthenticatedRoutes(): void
    {
        if (static::sendsOrganizationInvitations()) {
            Route::get('/invitations/{invitation}', [OrganizationInvitationController::class, 'accept'])
                ->middleware(['signed'])
                ->name('invitations.accept');
        }
    }

    public static function route(string $name, mixed $parameters = [], bool $absolute = true): string
    {
        return route(static::generateRouteName($name), $parameters, $absolute);
    }

    public static function generateRouteName(string $name): string
    {
        return 'filament.' . static::getOrganizationPanel() . ".{$name}";
    }

    public static function generateOAuthRedirectUrl(string $provider): string
    {
        return static::route('oauth.redirect', compact('provider'));
    }

    public static function generateAcceptInvitationUrl(OrganizationInvitation $invitation): string
    {
        return URL::signedRoute(static::generateRouteName('invitations.accept'), compact('invitation'));
    }
}
