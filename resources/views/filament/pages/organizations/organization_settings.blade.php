<x-filament-panels::page>
    @livewire(\Joinapi\FilamentOrganizations\Http\Livewire\UpdateOrganizationNameForm::class, compact('organization'))

    @livewire(\Joinapi\FilamentOrganizations\Http\Livewire\OrganizationEmployeeManager::class, compact('organization'))

    @if (!$organization->personal_organization && Gate::check('delete', $organization))
        <x-filament-organizations::section-border />
        @livewire(\Joinapi\FilamentOrganizations\Http\Livewire\DeleteOrganizationForm::class, compact('organization'))
    @endif
</x-filament-panels::page>
