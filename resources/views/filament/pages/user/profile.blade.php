<x-filament-panels::page>
    @php
        $components = \Joinapi\FilamentOrganizations\FilamentOrganizations::getProfileComponents();
    @endphp

    @foreach($components as $index => $component)
        @livewire($component)

        @if($loop->remaining)
            <x-filament-organizations::section-border />
        @endif
    @endforeach
</x-filament-panels::page>
