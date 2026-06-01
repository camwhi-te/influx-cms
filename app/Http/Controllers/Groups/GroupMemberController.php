<?php

namespace App\Http\Controllers\Groups;

use App\Enums\GroupRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Groups\UpdateGroupMemberRequest;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class GroupMemberController extends Controller
{
    /**
     * Update the specified group member's role.
     */
    public function update(UpdateGroupMemberRequest $request, Group $group, User $user): RedirectResponse
    {
        Gate::authorize('updateMember', $group);

        $newRole = GroupRole::from($request->validated('role'));

        $group->memberships()
            ->where('user_id', $user->id)
            ->firstOrFail()
            ->update(['role' => $newRole]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Member role updated.')]);

        return to_route('groups.edit', ['group' => $group->slug]);
    }

    /**
     * Remove the specified group member.
     */
    public function destroy(Group $group, User $user): RedirectResponse
    {
        Gate::authorize('removeMember', $group);

        abort_if($group->owner()?->is($user), 403, __('The group owner cannot be removed.'));

        $group->memberships()
            ->where('user_id', $user->id)
            ->delete();

        if ($user->isCurrentGroup($group)) {
            $user->switchGroup($user->personalGroup());
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Member removed.')]);

        return to_route('groups.edit', ['group' => $group->slug]);
    }
}
