<?php

namespace App\Http\Middleware;

use App\Enums\GroupRole;
use App\Models\Group;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureGroupMembership
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ?string $minimumRole = null): Response
    {
        [$user, $group] = [$request->user(), $this->group($request)];

        abort_if(! $user || ! $group || ! $user->belongsToGroup($group), 403);

        $this->ensureGroupMemberHasRequiredRole($user, $group, $minimumRole);

        if ($request->route('current_group') && ! $user->isCurrentGroup($group)) {
            $user->switchGroup($group);
        }

        return $next($request);
    }

    /**
     * Ensure the given user has at least the given role, if applicable.
     */
    protected function ensureGroupMemberHasRequiredRole(User $user, Group $group, ?string $minimumRole): void
    {
        if ($minimumRole === null) {
            return;
        }

        $role = $user->groupRole($group);

        $requiredRole = GroupRole::tryFrom($minimumRole);

        abort_if(
            $requiredRole === null ||
            $role === null ||
            ! $role->isAtLeast($requiredRole),
            403,
        );
    }

    /**
     * Get the group associated with the request.
     */
    protected function group(Request $request): ?Group
    {
        $group = $request->route('current_group') ?? $request->route('group');

        if (is_string($group)) {
            $group = Group::where('slug', $group)->first();
        }

        return $group;
    }
}
