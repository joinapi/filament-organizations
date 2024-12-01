<?php

namespace Joinapi\FilamentOrganizations\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\View\View;
use Joinapi\FilamentOrganizations\FilamentOrganizations;

enum Provider: string implements HasLabel
{
    case Bitbucket = 'bitbucket';
    case Facebook = 'facebook';
    case Gitlab = 'gitlab';
    case Github = 'github';
    case Google = 'google';
    case LinkedIn = 'linkedin';
    case LinkedInOpenId = 'linkedin-openid';
    case Slack = 'slack';
    case Twitter = 'twitter';
    case TwitterOAuth2 = 'twitter-oauth-2';

    public function getLabel(): string
    {
        return match ($this) {
            self::Bitbucket => 'Bitbucket',
            self::Facebook => 'Facebook',
            self::Gitlab => 'GitLab',
            self::Github => 'GitHub',
            self::Google => 'Google',
            self::LinkedIn, self::LinkedInOpenId => 'LinkedIn',
            self::Slack => 'Slack',
            self::Twitter, self::TwitterOAuth2 => 'X',
        };
    }

    public function isEnabled(): bool
    {
        return FilamentOrganizations::isProviderEnabled($this);
    }

    public function getIconView(): View
    {
        $viewName = match ($this) {
            self::Bitbucket => 'filament-organizations::components.socialite-icons.bitbucket',
            self::Facebook => 'filament-organizations::components.socialite-icons.facebook',
            self::Gitlab => 'filament-organizations::components.socialite-icons.gitlab',
            self::Github => 'filament-organizations::components.socialite-icons.github',
            self::Google => 'filament-organizations::components.socialite-icons.google',
            self::LinkedIn, self::LinkedInOpenId => 'filament-organizations::components.socialite-icons.linkedin',
            self::Slack => 'filament-organizations::components.socialite-icons.slack',
            self::Twitter, self::TwitterOAuth2 => 'filament-organizations::components.socialite-icons.twitter',
        };

        return view($viewName);
    }
}
