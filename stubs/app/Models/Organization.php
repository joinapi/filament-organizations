<?php

namespace App\Models;

use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Joinapi\FilamentOrganizations\Organization as FilamentOrganizationsOrganization;
use Joinapi\FilamentOrganizations\Events\OrganizationCreated;
use Joinapi\FilamentOrganizations\Events\OrganizationDeleted;
use Joinapi\FilamentOrganizations\Events\OrganizationUpdated;

class Organization extends FilamentOrganizationsOrganization implements HasAvatar
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'personal_organization',
    ];

    /**
     * The event map for the model.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => OrganizationCreated::class,
        'updated' => OrganizationUpdated::class,
        'deleted' => OrganizationDeleted::class,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'personal_organization' => 'boolean',
        ];
    }

    public function getFilamentAvatarUrl(): string
    {
        return $this->owner->profile_photo_url;
    }
}
