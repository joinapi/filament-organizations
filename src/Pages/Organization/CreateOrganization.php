<?php

namespace Joinapi\FilamentOrganizations\Pages\Organization;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Tenancy\RegisterTenant as FilamentRegisterTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Joinapi\FilamentOrganizations\Events\AddingOrganization;
use Joinapi\FilamentOrganizations\FilamentOrganizations;

class CreateOrganization extends FilamentRegisterTenant
{
    protected static string $view = 'filament-organizations::filament.pages.organizations.create_organization';

    public static function getLabel(): string
    {
        return __('filament-organizations::default.pages.titles.create_organization');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('filament-organizations::default.labels.organization_name'))
                    ->autofocus()
                    ->maxLength(255)
                    ->required(),
            ])
            ->model(FilamentOrganizations::organizationModel())
            ->statePath('data');
    }

    protected function handleRegistration(array $data): Model
    {
        $user = Auth::user();

        Gate::forUser($user)->authorize('create', FilamentOrganizations::newOrganizationModel());

        AddingOrganization::dispatch($user);

        $personalOrganization = $user?->personalOrganization() === null;

        $organization = $user?->ownedOrganizations()->create([
            'name' => $data['name'],
            'personal_organization' => $personalOrganization,
        ]);

        $user?->switchOrganization($organization);

        $name = $data['name'];

        $this->organizationCreated($name);

        return $organization;
    }

    protected function organizationCreated($name): void
    {
        Notification::make()
            ->title(__('filament-organizations::default.notifications.organization_created.title'))
            ->success()
            ->body(Str::inlineMarkdown(__('filament-organizations::default.notifications.organization_created.body', compact('name'))))
            ->send();
    }
}
