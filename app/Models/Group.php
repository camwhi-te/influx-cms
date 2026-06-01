<?php

namespace App\Models;

use App\Concerns\GeneratesUniqueGroupSlugs;
use App\Enums\GroupRole;
use Database\Factories\GroupFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'slug', 'is_personal'])]
class Group extends Model
{
    /** @use HasFactory<GroupFactory> */
    use GeneratesUniqueGroupSlugs, HasFactory, SoftDeletes;

    /**
     * Bootstrap the model and its traits.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Group $group) {
            if (empty($group->slug)) {
                $group->slug = static::generateUniqueGroupSlug($group->name);
            }
        });

        static::updating(function (Group $group) {
            if ($group->isDirty('name')) {
                $group->slug = static::generateUniqueGroupSlug($group->name, $group->id);
            }
        });
    }

    /**
     * Get the group owner.
     */
    public function owner(): ?Model
    {
        return $this->members()
            ->wherePivot('role', GroupRole::Owner->value)
            ->first();
    }

    /**
     * Get all members of this group.
     *
     * @return BelongsToMany<Model, $this>
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_members', 'group_id', 'user_id')
            ->using(Membership::class)
            ->withPivot(['role'])
            ->withTimestamps();
    }

    /**
     * Get all memberships for this group.
     *
     * @return HasMany<Membership, $this>
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    /**
     * Get all invitations for this group.
     *
     * @return HasMany<GroupInvitation, $this>
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(GroupInvitation::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_personal' => 'boolean',
        ];
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
