<?php

namespace App\Http\Controllers\Groups;

use App\Actions\Groups\CreateGroup;
use App\Enums\GroupRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Groups\DeleteGroupRequest;
use App\Http\Requests\Groups\SaveGroupRequest;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class GroupController extends Controller
{
    /**
     * Display a listing of the user's groups.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('groups/index', [
            'groups' => $user->toUserGroups(includeCurrent: true),
        ]);
    }

    /**
     * Store a newly created group.
     */
    public function store(SaveGroupRequest $request, CreateGroup $createGroup): RedirectResponse
    {
        $group = $createGroup->handle($request->user(), $request->validated('name'));

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Group created.')]);

        return to_route('groups.edit', ['group' => $group->slug]);
    }

    /**
     * Show the group edit page.
     */
    public function edit(Request $request, Group $group): Response
    {
        $user = $request->user();

        return Inertia::render('groups/edit', [
            'group' => [
                'id' => $group->id,
                'name' => $group->name,
                'slug' => $group->slug,
                'isPersonal' => $group->is_personal,
            ],
            'members' => $group->members()->get()->map(fn ($member) => [
                'id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
                'avatar' => $member->avatar ?? null,
                'role' => $member->pivot->role->value,
                'role_label' => $member->pivot->role->label(),
            ]),
            'invitations' => $group->invitations()
                ->whereNull('accepted_at')
                ->get()
                ->map(fn ($invitation) => [
                    'code' => $invitation->code,
                    'email' => $invitation->email,
                    'role' => $invitation->role->value,
                    'role_label' => $invitation->role->label(),
                    'created_at' => $invitation->created_at->toISOString(),
                ]),
            'permissions' => $user->toGroupPermissions($group),
            'availableRoles' => GroupRole::assignable(),
        ]);
    }

    /**
     * Update the specified group.
     */
    public function update(SaveGroupRequest $request, Group $group): RedirectResponse
    {
        Gate::authorize('update', $group);

        $group = DB::transaction(function () use ($request, $group) {
            $group = Group::whereKey($group->id)->lockForUpdate()->firstOrFail();

            $group->update(['name' => $request->validated('name')]);

            return $group;
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Group updated.')]);

        return to_route('groups.edit', ['group' => $group->slug]);
    }

    /**
     * Switch the user's current group.
     */
    public function switch(Request $request, Group $group): RedirectResponse
    {
        abort_unless($request->user()->belongsToGroup($group), 403);

        $request->user()->switchGroup($group);

        return back();
    }

    /**
     * Delete the specified group.
     */
    public function destroy(DeleteGroupRequest $request, Group $group): RedirectResponse
    {
        $user = $request->user();
        $fallbackGroup = $user->isCurrentGroup($group)
            ? $user->fallbackGroup($group)
            : null;

        DB::transaction(function () use ($user, $group) {
            User::where('current_group_id', $group->id)
                ->where('id', '!=', $user->id)
                ->each(fn (User $affectedUser) => $affectedUser->switchGroup($affectedUser->personalGroup()));

            $group->invitations()->delete();
            $group->memberships()->delete();
            $group->delete();
        });

        if ($fallbackGroup) {
            $user->switchGroup($fallbackGroup);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Group deleted.')]);

        return to_route('groups.index');
    }
}
