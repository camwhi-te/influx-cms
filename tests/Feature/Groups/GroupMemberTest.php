<?php

namespace Tests\Feature\Groups;

use App\Enums\GroupRole;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupMemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_group_member_roles_can_be_updated_by_owners()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $group = Group::factory()->create();

        $group->members()->attach($owner, ['role' => GroupRole::Owner->value]);
        $group->members()->attach($member, ['role' => GroupRole::Member->value]);

        $response = $this
            ->actingAs($owner)
            ->patch(route('groups.members.update', [$group, $member]), [
                'role' => GroupRole::Admin->value,
            ]);

        $response->assertRedirect(route('groups.edit', $group));

        $this->assertEquals(
            GroupRole::Admin->value,
            $group->members()->where('user_id', $member->id)->first()->pivot->role->value,
        );
    }

    public function test_group_member_roles_cannot_be_updated_by_non_owners()
    {
        $owner = User::factory()->create();
        $admin = User::factory()->create();
        $member = User::factory()->create();
        $group = Group::factory()->create();

        $group->members()->attach($owner, ['role' => GroupRole::Owner->value]);
        $group->members()->attach($admin, ['role' => GroupRole::Admin->value]);
        $group->members()->attach($member, ['role' => GroupRole::Member->value]);

        $response = $this
            ->actingAs($admin)
            ->patch(route('groups.members.update', [$group, $member]), [
                'role' => GroupRole::Admin->value,
            ]);

        $response->assertForbidden();
    }

    public function test_group_members_can_be_removed_by_owners()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $group = Group::factory()->create();

        $group->members()->attach($owner, ['role' => GroupRole::Owner->value]);
        $group->members()->attach($member, ['role' => GroupRole::Member->value]);

        $response = $this
            ->actingAs($owner)
            ->delete(route('groups.members.destroy', [$group, $member]));

        $response->assertRedirect(route('groups.edit', $group));

        $this->assertFalse($member->fresh()->belongsToGroup($group));
    }

    public function test_group_members_cannot_be_removed_by_non_owners()
    {
        $owner = User::factory()->create();
        $admin = User::factory()->create();
        $member = User::factory()->create();
        $group = Group::factory()->create();

        $group->members()->attach($owner, ['role' => GroupRole::Owner->value]);
        $group->members()->attach($admin, ['role' => GroupRole::Admin->value]);
        $group->members()->attach($member, ['role' => GroupRole::Member->value]);

        $response = $this
            ->actingAs($admin)
            ->delete(route('groups.members.destroy', [$group, $member]));

        $response->assertForbidden();
    }

    public function test_group_owner_cannot_be_removed()
    {
        $owner = User::factory()->create();
        $group = Group::factory()->create();

        $group->members()->attach($owner, ['role' => GroupRole::Owner->value]);

        $response = $this
            ->actingAs($owner)
            ->delete(route('groups.members.destroy', [$group, $owner]));

        $response->assertForbidden();

        $this->assertTrue($owner->fresh()->belongsToGroup($group));
    }

    public function test_group_member_role_cannot_be_set_to_owner()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $group = Group::factory()->create();

        $group->members()->attach($owner, ['role' => GroupRole::Owner->value]);
        $group->members()->attach($member, ['role' => GroupRole::Member->value]);

        $response = $this
            ->actingAs($owner)
            ->patch(route('groups.members.update', [$group, $member]), [
                'role' => GroupRole::Owner->value,
            ]);

        $response->assertSessionHasErrors('role');

        $this->assertEquals(
            GroupRole::Member->value,
            $group->members()->where('user_id', $member->id)->first()->pivot->role->value,
        );
    }

    public function test_removed_member_current_group_is_set_to_personal_group()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $personalGroup = $member->personalGroup();
        $group = Group::factory()->create();

        $group->members()->attach($owner, ['role' => GroupRole::Owner->value]);
        $group->members()->attach($member, ['role' => GroupRole::Member->value]);

        $member->update(['current_group_id' => $group->id]);

        $this
            ->actingAs($owner)
            ->delete(route('groups.members.destroy', [$group, $member]));

        $this->assertEquals($personalGroup->id, $member->fresh()->current_group_id);
    }
}
