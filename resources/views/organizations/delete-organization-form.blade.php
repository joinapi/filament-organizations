@php
    $modals = \Joinapi\FilamentOrganizations\FilamentOrganizations::getModals();
@endphp

<x-filament-organizations::grid-section md="2">
    <x-slot name="title">
        {{ __('filament-organizations::default.action_section_titles.delete_organization') }}
    </x-slot>

    <x-slot name="description">
        {{ __('filament-organizations::default.action_section_descriptions.delete_organization') }}
    </x-slot>

    <x-filament::section>
        <div class="grid gap-y-6">
            <div class="max-w-xl text-sm text-gray-600 dark:text-gray-400">
                {{ __('filament-organizations::default.subheadings.organizations.delete_organization') }}
            </div>

            <!-- Delete Organization Confirmation Modal -->
            <x-filament::modal id="confirmingOrganizationDeletion" icon="heroicon-o-exclamation-triangle" icon-color="danger" alignment="{{ $modals['alignment'] }}" footer-actions-alignment="{{ $modals['formActionsAlignment'] }}" width="{{ $modals['width'] }}">
                <x-slot name="trigger">
                    <div class="text-left">
                        <x-filament::button color="danger">
                            {{ __('filament-organizations::default.buttons.delete_organization') }}
                        </x-filament::button>
                    </div>
                </x-slot>

                <x-slot name="heading">
                    {{ __('filament-organizations::default.modal_titles.delete_organization') }}
                </x-slot>

                <x-slot name="description">
                    {{ __('filament-organizations::default.modal_descriptions.delete_organization') }}
                </x-slot>

                <x-slot name="footerActions">
                    @if($modals['cancelButtonAction'])
                        <x-filament::button color="gray" wire:click="cancelOrganizationDeletion">
                            {{ __('filament-organizations::default.buttons.cancel') }}
                        </x-filament::button>
                    @endif

                    <x-filament::button color="danger" wire:click="deleteOrganization">
                        {{ __('filament-organizations::default.buttons.delete_organization') }}
                    </x-filament::button>
                </x-slot>
            </x-filament::modal>
        </div>
    </x-filament::section>
</x-filament-organizations::grid-section>
