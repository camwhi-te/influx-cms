<?php

namespace App\Http\Controllers\Groups;

use App\Enums\GroupRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Groups\AcceptGroupInvitationRequest;
use App\Http\Requests\Groups\CreateGroupInvitationRequest;
use App\Models\Group;
use App\Models\GroupInvitation;
use App\Notifications\Groups\GroupInvitation as GroupInvitationNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;

class GroupInvitationController extends Controller
{
    /**
     * Store a newly created invitation.
     */
    public function store(CreateGroupInvitationRequest $request, Group $group): RedirectResponse
    {
        Gate::authorize('inviteMember', $group);

        $invitation = $group->invitations()->create([
            'email' => $request->validated('email'),
            'role' => GroupRole::from($request->validated('role')),
            'invited_by' => $request->user()->id,
            'expires_at' => now()->addDays(3),
        ]);

        Notification::route('mail', $invitation->email)
            ->notify(new GroupInvitationNotification($invitation));

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Invitation sent.')]);

        return to_route('groups.edit', ['group' => $group->slug]);
    }

    /**
     * Cancel the specified invitation.
     */
    public function destroy(Group $group, GroupInvitation $invitation): RedirectResponse
    {
        abort_unless($invitation->group_id === $group->id, 404);

        Gate::authorize('cancelInvitation', $group);

        $invitation->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Invitation cancelled.')]);

        return to_route('groups.edit', ['group' => $group->slug]);
    }

    /**
     * Accept the invitation.
     */
    public function accept(AcceptGroupInvitationRequest $request, GroupInvitation $invitation): RedirectResponse
    {
        $user = $request->user();

        DB::transaction(function () use ($user, $invitation) {
            $group = $invitation->group;

            $group->memberships()->firstOrCreate(
                ['user_id' => $user->id],
                ['role' => $invitation->role],
            );

            $invitation->update(['accepted_at' => now()]);

            $user->switchGroup($group);
        });

        return to_route('dashboard');
    }
}
