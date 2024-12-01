<?php

namespace Joinapi\FilamentOrganizations\Concerns\Base;

use Filament\Facades\Filament;

trait HasPanels
{
    /**
     * The user panel.
     */
    public static ?string $userPanel = null;

    /**
     * Set the user panel.
     */
    public function userPanel(string $panel): static
    {
        static::$userPanel = $panel;

        return $this;
    }

    /**
     * Get the user panel configuration.
     */
    public static function getUserPanel(): string
    {
        return static::$userPanel;
    }

    /**
     * Determine if the user panel is set.
     */
    public static function hasUserPanel(): bool
    {
        return static::$userPanel !== null;
    }

    /**
     * Get the panel where the plugin is registered (The organization panel).
     */
    public static function getOrganizationPanel(): ?string
    {
        foreach (Filament::getPanels() as $panel) {
            if ($panel->hasPlugin('organizations')) {
                return $panel->getId();
            }
        }

        return null;
    }
}