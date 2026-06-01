<?php

namespace App\Actions\Groups;

use App\Enums\GroupRole;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateGroup
{
    /**
     * Create a new group and add the user as owner.
     */
    public function handle(User $user, string $name, bool $isPersonal = false): Group
    {
        return DB::transaction(function () use ($user, $name, $isPersonal) {
            $group = Group::create([
                'name' => $name,
                'is_personal' => $isPersonal,
            ]);

            $membership = $group->memberships()->create([
                'user_id' => $user->id,
                'role' => GroupRole::Owner,
            ]);

            $user->switchGroup($group);

            return $group;
        });
    }
}
