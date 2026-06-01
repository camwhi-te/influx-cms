<?php

namespace Tests\Feature\Groups;

use App\Enums\GroupRole;
use App\Models\Group;
use App\Models\GroupInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class GroupInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_group_invitations_can_be_created()
    {
        Notification::fake();

        $owner = User::factory()->create();
        $group = Group::factory()->create();

        $group->members()->attach($owner, ['role' => GroupRole::Owner->value]);

        $response = $this
            ->actingAs($owner)
            ->post(route('groups.invitations.store', $group), [
                'email' => 'invited@example.com',
                'role' => GroupRole::Member->value,
            ]);

        $response->assertRedirect(route('groups.edit', $group));

        $this->assertDatabaseHas('group_invitations', [
            'group_id' => $group->id,
            'email' => 'invited@example.com',
            'role' => GroupRole::Member->value,
        ]);
    }

    public function test_group_invitations_can_be_created_by_admins()
    {
        Notification::fake();

        $owner = User::factory()->create();
        $admin = User::factory()->create();
        $group = Group::factory()->create();

        $group->members()->attach($owner, ['role' => GroupRole::Owner->value]);
        $group->members()->attach($admin, ['role' => GroupRole::Admin->value]);

        $response = $this
            ->actingAs($admin)
            ->post(route('groups.invitations.store', $group), [
                'email' => 'invited@example.com',
                'role' => GroupRole::Member->value,
            ]);

        $response->assertRedirect(route('groups.edit', $group));
    }

    public function test_existing_group_members_cannot_be_invited()
    {
        Notification::fake();

        $owner = User::factory()->create();
        $member = User::factory()->create(['email' => 'member@example.com']);
        $group = Group::factory()->create();

        $group->members()->attach($owner, ['role' => GroupRole::Owner->value]);
        $group->members()->attach($member, ['role' => GroupRole::Member->value]);

        $response = $this
            ->actingAs($owner)
            ->post(route('groups.invitations.store', $group), [
                'email' => 'member@example.com',
                'role' => GroupRole::Member->value,
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_duplicate_invitations_cannot_be_created()
    {
        Notification::fake();

        $owner = User::factory()->create();
        $group = Group::factory()->create();
        $group->members()->attach($owner, ['role' => GroupRole::Owner->value]);

        GroupInvitation::factory()->create([
            'group_id' => $group->id,
            'email' => 'invited@example.com',
            'invited_by' => $owner->id,
        ]);

        $response = $this
            ->actingAs($owner)
            ->post(route('groups.invitations.store', $group), [
                'email' => 'invited@example.com',
                'role' => GroupRole::Member->value,
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_group_invitations_cannot_be_created_by_members()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $group = Group::factory()->create();

        $group->members()->attach($owner, ['role' => GroupRole::Owner->value]);
        $group->members()->attach($member, ['role' => GroupRole::Member->value]);

        $response = $this
            ->actingAs($member)
            ->post(route('groups.invitations.store', $group), [
                'email' => 'invited@example.com',
                'role' => GroupRole::Member->value,
            ]);

        $response->assertForbidden();
    }

    public function test_group_invitations_can_be_cancelled_by_owners()
    {
        $owner = User::factory()->create();
        $group = Group::factory()->create();

        $group->members()->attach($owner, ['role' => GroupRole::Owner->value]);

        $invitation = GroupInvitation::factory()->create([
            'group_id' => $group->id,
            'invited_by' => $owner->id,
        ]);

        $response = $this
            ->actingAs($owner)
            ->delete(route('groups.invitations.destroy', [$group, $invitation]));

        $response->assertRedirect(route('groups.edit', $group));

        $this->assertDatabaseMissing('group_invitations', [
            'id' => $invitation->id,
        ]);
    }

    public function test_group_invitations_can_be_accepted()
    {
        $owner = User::factory()->create();
        $invitedUser = User::factory()->create(['email' => 'invited@example.com']);
        $group = Group::factory()->create();

        $group->members()->attach($owner, ['role' => GroupRole::Owner->value]);

        $invitation = GroupInvitation::factory()->create([
            'group_id' => $group->id,
            'email' => 'invited@example.com',
            'role' => GroupRole::Member,
            'invited_by' => $owner->id,
        ]);

        $response = $this
            ->actingAs($invitedUser)
            ->get(route('invitations.accept', $invitation));

        $response->assertRedirect(route('dashboard'));

        $this->assertTrue($invitedUser->fresh()->belongsToGroup($group));
        $this->assertNotNull($invitation->fresh()->accepted_at);
    }

    public function test_group_invitations_cannot_be_accepted_by_uninvited_user()
    {
        $owner = User::factory()->create();
        $uninvitedUser = User::factory()->create(['email' => 'uninvited@example.com']);
        $group = Group::factory()->create();

        $group->members()->attach($owner, ['role' => GroupRole::Owner->value]);

        $invitation = GroupInvitation::factory()->create([
            'group_id' => $group->id,
            'email' => 'invited@example.com',
            'invited_by' => $owner->id,
        ]);

        $response = $this
            ->actingAs($uninvitedUser)
            ->get(route('invitations.accept', $invitation));

        $response->assertSessionHasErrors('invitation');

        $this->assertFalse($uninvitedUser->fresh()->belongsToGroup($group));
    }

    public function test_expired_invitations_cannot_be_accepted()
    {
        $owner = User::factory()->create();
        $invitedUser = User::factory()->create(['email' => 'invited@example.com']);
        $group = Group::factory()->create();

        $group->members()->attach($owner, ['role' => GroupRole::Owner->value]);

        $invitation = GroupInvitation::factory()->expired()->create([
            'group_id' => $group->id,
            'email' => 'invited@example.com',
            'invited_by' => $owner->id,
        ]);

        $response = $this
            ->actingAs($invitedUser)
            ->get(route('invitations.accept', $invitation));

        $response->assertSessionHasErrors('invitation');

        $this->assertFalse($invitedUser->fresh()->belongsToGroup($group));
    }
}
