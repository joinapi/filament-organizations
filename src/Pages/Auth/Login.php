<?php

namespace Joinapi\FilamentOrganizations\Pages\Auth;

use Filament\Forms\Form;
use Filament\Pages\Auth\Login as FilamentLogin;
use Joinapi\FilamentOrganizations\FilamentOrganizations;

class Login extends FilamentLogin
{
    public static string $view = 'filament-organizations::auth.login';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data')
            ->model(FilamentOrganizations::userModel());
    }
}
