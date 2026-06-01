<?php

namespace Tests\Feature\Groups;

use App\Enums\GroupRole;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_groups_index_page_can_be_rendered()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('groups.index'));

        $response->assertOk();
    }

    public function test_groups_can_be_created()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('groups.store'), [
                'name' => 'Test Group',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('groups', [
            'name' => 'Test Group',
            'is_personal' => false,
        ]);
    }

    public function test_group_slug_uses_next_available_suffix()
    {
        $user = User::factory()->create();

        Group::factory()->create(['name' => 'Acme', 'slug' => 'acme']);
        Group::factory()->create(['name' => 'Acme One', 'slug' => 'acme-1']);
        Group::factory()->create(['name' => 'Acme Ten', 'slug' => 'acme-10']);

        $this
            ->actingAs($user)
            ->post(route('groups.store'), [
                'name' => 'Acme',
            ]);

        $this->assertDatabaseHas('groups', [
            'name' => 'Acme',
            'slug' => 'acme-11',
        ]);
    }

    public function test_the_group_edit_page_can_be_rendered()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();

        $group->members()->attach($user, ['role' => GroupRole::Owner->value]);

        $response = $this
            ->actingAs($user)
            ->get(route('groups.edit', $group));

        $response->assertOk();
    }

    public function test_groups_can_be_updated_by_owners()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create(['name' => 'Original Name']);

        $group->members()->attach($user, ['role' => GroupRole::Owner->value]);

        $response = $this
            ->actingAs($user)
            ->patch(route('groups.update', $group), [
                'name' => 'Updated Name',
            ]);

        $response->assertRedirect(route('groups.edit', $group->fresh()));

        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_groups_cannot_be_updated_by_members()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $group = Group::factory()->create();

        $group->members()->attach($owner, ['role' => GroupRole::Owner->value]);
        $group->members()->attach($member, ['role' => GroupRole::Member->value]);

        $response = $this
            ->actingAs($member)
            ->patch(route('groups.update', $group), [
                'name' => 'Updated Name',
            ]);

        $response->assertForbidden();
    }

    public function test_groups_can_be_deleted_by_owners()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();

        $group->members()->attach($user, ['role' => GroupRole::Owner->value]);

        $response = $this
            ->actingAs($user)
            ->delete(route('groups.destroy', $group), [
                'name' => $group->name,
            ]);

        $response->assertRedirect();

        $this->assertSoftDeleted('groups', [
            'id' => $group->id,
        ]);
    }

    public function test_group_deletion_requires_name_confirmation()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();

        $group->members()->attach($user, ['role' => GroupRole::Owner->value]);

        $response = $this
            ->actingAs($user)
            ->delete(route('groups.destroy', $group), [
                'name' => 'Wrong Name',
            ]);

        $response->assertSessionHasErrors('name');

        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'deleted_at' => null,
        ]);
    }

    public function test_deleting_current_group_switches_to_alphabetically_first_remaining_group()
    {
        $user = User::factory()->create(['name' => 'Mike']);

        $zuluGroup = Group::factory()->create(['name' => 'Zulu Group']);
        $zuluGroup->members()->attach($user, ['role' => GroupRole::Owner->value]);

        $alphaGroup = Group::factory()->create(['name' => 'Alpha Group']);
        $alphaGroup->members()->attach($user, ['role' => GroupRole::Owner->value]);

        $betaGroup = Group::factory()->create(['name' => 'Beta Group']);
        $betaGroup->members()->attach($user, ['role' => GroupRole::Owner->value]);

        $user->update(['current_group_id' => $zuluGroup->id]);

        $response = $this
            ->actingAs($user)
            ->delete(route('groups.destroy', $zuluGroup), [
                'name' => $zuluGroup->name,
            ]);

        $response->assertRedirect();

        $this->assertSoftDeleted('groups', [
            'id' => $zuluGroup->id,
        ]);

        $this->assertEquals($alphaGroup->id, $user->fresh()->current_group_id);
    }

    public function test_deleting_current_group_falls_back_to_personal_group_when_alphabetically_first()
    {
        $user = User::factory()->create();
        $personalGroup = $user->personalGroup();
        $group = Group::factory()->create(['name' => 'Zulu Group']);
        $group->members()->attach($user, ['role' => GroupRole::Owner->value]);

        $user->update(['current_group_id' => $group->id]);

        $response = $this
            ->actingAs($user)
            ->delete(route('groups.destroy', $group), [
                'name' => $group->name,
            ]);

        $response->assertRedirect();

        $this->assertSoftDeleted('groups', [
            'id' => $group->id,
        ]);

        $this->assertEquals($personalGroup->id, $user->fresh()->current_group_id);
    }

    public function test_deleting_non_current_group_leaves_current_group_unchanged()
    {
        $user = User::factory()->create();
        $personalGroup = $user->personalGroup();
        $group = Group::factory()->create();
        $group->members()->attach($user, ['role' => GroupRole::Owner->value]);

        $user->update(['current_group_id' => $personalGroup->id]);

        $response = $this
            ->actingAs($user)
            ->delete(route('groups.destroy', $group), [
                'name' => $group->name,
            ]);

        $response->assertRedirect();

        $this->assertSoftDeleted('groups', [
            'id' => $group->id,
        ]);

        $this->assertEquals($personalGroup->id, $user->fresh()->current_group_id);
    }

    public function test_deleting_group_switches_other_affected_users_to_their_personal_group()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();

        $group = Group::factory()->create();
        $group->members()->attach($owner, ['role' => GroupRole::Owner->value]);
        $group->members()->attach($member, ['role' => GroupRole::Member->value]);

        $owner->update(['current_group_id' => $group->id]);
        $member->update(['current_group_id' => $group->id]);

        $response = $this
            ->actingAs($owner)
            ->delete(route('groups.destroy', $group), [
                'name' => $group->name,
            ]);

        $response->assertRedirect();

        $this->assertEquals($member->personalGroup()->id, $member->fresh()->current_group_id);
    }

    public function test_personal_groups_cannot_be_deleted()
    {
        $user = User::factory()->create();

        $personalGroup = $user->personalGroup();

        $response = $this
            ->actingAs($user)
            ->delete(route('groups.destroy', $personalGroup), [
                'name' => $personalGroup->name,
            ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('groups', [
            'id' => $personalGroup->id,
            'deleted_at' => null,
        ]);
    }

    public function test_groups_cannot_be_deleted_by_non_owners()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $group = Group::factory()->create();

        $group->members()->attach($owner, ['role' => GroupRole::Owner->value]);
        $group->members()->attach($member, ['role' => GroupRole::Member->value]);

        $response = $this
            ->actingAs($member)
            ->delete(route('groups.destroy', $group), [
                'name' => $group->name,
            ]);

        $response->assertForbidden();
    }

    public function test_users_can_switch_groups()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();

        $group->members()->attach($user, ['role' => GroupRole::Member->value]);

        $response = $this
            ->actingAs($user)
            ->post(route('groups.switch', $group));

        $response->assertRedirect();

        $this->assertEquals($group->id, $user->fresh()->current_group_id);
    }

    public function test_users_cannot_switch_to_group_they_dont_belong_to()
    {
        $user = User::factory()->create();
        $group = Group::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('groups.switch', $group));

        $response->assertForbidden();
    }

    public function test_guests_cannot_access_groups()
    {
        $response = $this->get(route('groups.index'));

        $response->assertRedirect(route('login'));
    }
}
