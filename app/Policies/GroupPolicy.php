<?php

namespace App\Policies;

use App\Enums\GroupPermission;
use App\Models\Group;
use App\Models\User;

class GroupPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Group $group): bool
    {
        return $user->belongsToGroup($group);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Group $group): bool
    {
        return $user->hasGroupPermission($group, GroupPermission::UpdateGroup);
    }

    /**
     * Determine whether the user can add a member to the group.
     */
    public function addMember(User $user, Group $group): bool
    {
        return $user->hasGroupPermission($group, GroupPermission::AddMember);
    }

    /**
     * Determine whether the user can update a member's role in the group.
     */
    public function updateMember(User $user, Group $group): bool
    {
        return $user->hasGroupPermission($group, GroupPermission::UpdateMember);
    }

    /**
     * Determine whether the user can remove a member from the group.
     */
    public function removeMember(User $user, Group $group): bool
    {
        return $user->hasGroupPermission($group, GroupPermission::RemoveMember);
    }

    /**
     * Determine whether the user can invite members to the group.
     */
    public function inviteMember(User $user, Group $group): bool
    {
        return $user->hasGroupPermission($group, GroupPermission::CreateInvitation);
    }

    /**
     * Determine whether the user can cancel invitations.
     */
    public function cancelInvitation(User $user, Group $group): bool
    {
        return $user->hasGroupPermission($group, GroupPermission::CancelInvitation);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Group $group): bool
    {
        return ! $group->is_personal && $user->hasGroupPermission($group, GroupPermission::DeleteGroup);
    }
}
