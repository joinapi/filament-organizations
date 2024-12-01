<?php

namespace Joinapi\FilamentOrganizations\Http\Controllers;

use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Features\SupportRedirects\Redirector;
use Joinapi\FilamentOrganizations\Contracts\AddsOrganizationEmployees;
use Joinapi\FilamentOrganizations\FilamentOrganizations;

class OrganizationInvitationController extends Controller
{
    /**
     * Accept a organization invitation.
     */
    public function accept(Request $request, int $invitationId): Redirector | RedirectResponse | null
    {
        $model = FilamentOrganizations::organizationInvitationModel();

        $invitation = $model::whereKey($invitationId)->firstOrFail();
        $user = FilamentOrganizations::userModel()::where('email', $invitation->email)->first();

        app(AddsOrganizationEmployees::class)->add(
            $invitation->organization->owner,
            $invitation->organization,
            $invitation->email,
            $invitation->role
        );

        $invitation->delete();

        $title = __('filament-organizations::default.banner.organization_invitation_accepted', ['organization' => $invitation->organization->name]);
        $notification = Notification::make()->title(Str::inlineMarkdown($title))->success()->persistent()->send();

        if ($user) {
            Filament::auth()->login($user);

            return redirect(url(filament()->getHomeUrl()))->with('notification.success.organization_invitation_accepted', $notification);
        }

        return redirect(url(filament()->getLoginUrl()));
    }

    /**
     * Cancel the given organization invitation.
     *
     * @throws AuthorizationException
     */
    public function destroy(Request $request, int $invitationId): Redirector | RedirectResponse
    {
        $model = FilamentOrganizations::organizationInvitationModel();

        $invitation = $model::whereKey($invitationId)->firstOrFail();

        if (! Gate::forUser($request->user())->check('removeOrganizationEmployee', $invitation->organization)) {
            throw new AuthorizationException;
        }

        $invitation->delete();

        return back(303);
    }
}
