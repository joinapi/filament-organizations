<x-filament-organizations::grid-section md="2">
    <x-slot name="title">
        {{ __('filament-organizations::default.grid_section_titles.organization_name') }}
    </x-slot>

    <x-slot name="description">
        {{ __('filament-organizations::default.grid_section_descriptions.organization_name') }}
    </x-slot>

    <x-filament::section>
        <x-filament-panels::form wire:submit="updateOrganizationName">
                <!-- Organization Owner Information -->
                <x-filament-forms::field-wrapper.label>
                    {{ __('filament-organizations::default.labels.organization_owner') }}
                </x-filament-forms::field-wrapper.label>

                <div class="flex items-center text-sm">
                    <div class="flex-shrink-0">
                        <x-filament-panels::avatar.user :user="$organization->owner" style="height: 3rem; width: 3rem;" />
                    </div>
                    <div class="ml-4">
                        <div class="font-medium text-gray-900 dark:text-gray-200">{{ $organization->owner->name }}</div>
                        <div class="text-gray-600 dark:text-gray-400">{{ $organization->owner->email }}</div>
                    </div>
                </div>

                <!-- Organization Name -->
                <x-filament-forms::field-wrapper id="name" statePath="name" required="required" label="{{ __('filament-organizations::default.labels.organization_name') }}">
                    <x-filament::input.wrapper class="overflow-hidden">
                        <x-filament::input id="name" type="text" maxlength="255" wire:model="state.name" :disabled="!Gate::check('update', $organization)" />
                    </x-filament::input.wrapper>
                </x-filament-forms::field-wrapper>

                @if (Gate::check('update', $organization))
                    <div class="text-left">
                        <x-filament::button type="submit">
                            {{ __('filament-organizations::default.buttons.save') }}
                        </x-filament::button>
                    </div>
                @endif
        </x-filament-panels::form>
    </x-filament::section>
</x-filament-organizations::grid-section>
