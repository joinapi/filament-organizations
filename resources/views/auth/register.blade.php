<x-filament-panels::page.simple>
    @if (filament()->hasLogin())
        <x-slot name="subheading">
            {{ __('filament-panels::pages/auth/register.actions.login.before') }}

            {{ $this->loginAction }}
        </x-slot>
    @endif

    <x-filament-panels::form wire:submit="register">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    @if (\Joinapi\FilamentOrganizations\FilamentOrganizations::hasSocialiteFeatures())
        <x-filament-organizations::socialite :error-message="$errors->first('filament-organizations')" />
    @endif
</x-filament-panels::page.simple>
