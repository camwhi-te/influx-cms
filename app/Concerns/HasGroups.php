<?php

namespace App\Concerns;

use App\Enums\GroupPermission;
use App\Enums\GroupRole;
use App\Models\Membership;
use App\Models\Group;
use App\Support\GroupPermissions;
use App\Support\UserGroup;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;

trait HasGroups
{
    /**
     * Get all of the groups the user belongs to.
     *
     * @return BelongsToMany<Group, $this>
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_members', 'user_id', 'group_id')
            ->withPivot(['role'])
            ->withTimestamps();
    }

    /**
     * Get all of the groups the user owns.
     *
     * @return HasManyThrough<Group, Membership, $this>
     */
    public function ownedGroups(): HasManyThrough
    {
        return $this->hasManyThrough(
            Group::class,
            Membership::class,
            'user_id',
            'id',
            'id',
            'group_id',
        )->where('group_members.role', GroupRole::Owner->value);
    }

    /**
     * Get all of the memberships for the user.
     *
     * @return HasMany<Membership, $this>
     */
    public function groupMemberships(): HasMany
    {
        return $this->hasMany(Membership::class, 'user_id');
    }

    /**
     * Get the user's current group.
     *
     * @return BelongsTo<Group, $this>
     */
    public function currentGroup(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'current_group_id');
    }

    /**
     * Get the user's personal group.
     */
    public function personalGroup(): ?Group
    {
        return $this->groups()
            ->where('is_personal', true)
            ->first();
    }

    /**
     * Switch to the given group.
     */
    public function switchGroup(Group $group): bool
    {
        if (! $this->belongsToGroup($group)) {
            return false;
        }

        $this->update(['current_group_id' => $group->id]);
        $this->setRelation('currentGroup', $group);

        URL::defaults(['current_group' => $group->slug]);

        return true;
    }

    /**
     * Determine if the user belongs to the given group.
     */
    public function belongsToGroup(Group $group): bool
    {
        return $this->groups()->where('groups.id', $group->id)->exists();
    }

    /**
     * Determine if the given group is the user's current group.
     */
    public function isCurrentGroup(Group $group): bool
    {
        return $this->current_group_id === $group->id;
    }

    /**
     * Determine if the user is the owner of the given group.
     */
    public function ownsGroup(Group $group): bool
    {
        return $this->groupRole($group) === GroupRole::Owner;
    }

    /**
     * Get the user's role on the given group.
     */
    public function groupRole(Group $group): ?GroupRole
    {
        return $this->groupMemberships()
            ->where('group_id', $group->id)
            ->first()
            ?->role;
    }

    /**
     * Get the user's groups as a collection of UserGroup objects.
     *
     * @return Collection<int, UserGroup>
     */
    public function toUserGroups(bool $includeCurrent = false): Collection
    {
        return $this->groups()
            ->get()
            ->map(fn (Group $group) => ! $includeCurrent && $this->isCurrentGroup($group) ? null : $this->toUserGroup($group))
            ->filter()
            ->values();
    }

    /**
     * Get the user's group as a UserGroup object.
     */
    public function toUserGroup(Group $group): UserGroup
    {
        $role = $this->groupRole($group);

        return new UserGroup(
            id: $group->id,
            name: $group->name,
            slug: $group->slug,
            isPersonal: $group->is_personal,
            role: $role?->value,
            roleLabel: $role?->label(),
            isCurrent: $this->isCurrentGroup($group),
        );
    }

    /**
     * Get the standard permissions for a group as a GroupPermissions object.
     */
    public function toGroupPermissions(Group $group): GroupPermissions
    {
        $role = $this->groupRole($group);

        return new GroupPermissions(
            canUpdateGroup: $role?->hasPermission(GroupPermission::UpdateGroup) ?? false,
            canDeleteGroup: $role?->hasPermission(GroupPermission::DeleteGroup) ?? false,
            canAddMember: $role?->hasPermission(GroupPermission::AddMember) ?? false,
            canUpdateMember: $role?->hasPermission(GroupPermission::UpdateMember) ?? false,
            canRemoveMember: $role?->hasPermission(GroupPermission::RemoveMember) ?? false,
            canCreateInvitation: $role?->hasPermission(GroupPermission::CreateInvitation) ?? false,
            canCancelInvitation: $role?->hasPermission(GroupPermission::CancelInvitation) ?? false,
        );
    }

    public function fallbackGroup(?Group $excluding = null): ?Group
    {
        return $this->groups()
            ->when($excluding, fn ($query) => $query->where('groups.id', '!=', $excluding->id))
            ->orderByRaw('LOWER(groups.name)')
            ->first();
    }

    /**
     * Determine if the user has the given permission on the group.
     */
    public function hasGroupPermission(Group $group, GroupPermission $permission): bool
    {
        return $this->groupRole($group)?->hasPermission($permission) ?? false;
    }
}
