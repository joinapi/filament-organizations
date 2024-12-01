<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Joinapi\FilamentOrganizations\FilamentOrganizations;

class OrganizationInvitation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'role',
    ];

    /**
     * Get the organization that the invitation belongs to.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(FilamentOrganizations::organizationModel());
    }
}
