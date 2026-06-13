<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdminRole extends Model
{
    /** @use HasFactory<\Database\Factories\AdminRoleFactory> */
    use HasFactory;

    protected $fillable = ['title', 'description', 'permissions'];

    protected $casts = [
        'permissions' => 'array',
    ];

    /**
     * Get the users that have this admin role.
     */
    public function users(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Define all available permission keys.
     * Customize this array with your application's permission keys.
     * Format: 'resource:action' (e.g., 'user:create', 'user:delete')
     */
    public static function availablePermissions(): array
    {
        return [
            'overview:r',

            'user:c',
            'user:r',
            'user:u',
            'user:d',
        ];
    }

    /**
     * Check if the admin role has a specific permission.
     * Only returns true if the permission is explicitly set to true.
     * Anything else (null, false, missing) is considered denied.
     */
    public function hasPermission(string $permission): bool
    {
        if (!$this->permissions) {
            return false;
        }

        return $this->permissions[$permission] === true;
    }

    /**
     * Grant a permission to this admin role.
     */
    public function grantPermission(string $permission): self
    {
        $permissions = $this->permissions ?? [];
        $permissions[$permission] = true;
        $this->permissions = $permissions;

        $this->save();
    
        return $this;
    }

    /**
     * Grant multiple permissions at once.
     */
    public function grantPermissions(array $permissions): self
    {
        foreach ($permissions as $permission) {
            $perms = $this->permissions ?? [];
            $perms[$permission] = true;
            $this->permissions = $perms;
        }

        $this->save();

        return $this;
    }

    /**
     * Revoke a permission from this admin role.
     */
    public function revokePermission(string $permission): self
    {
        if (!$this->permissions) {
            return $this;
        }

        $permissions = $this->permissions;
        unset($permissions[$permission]);
        $this->permissions = $permissions;

        $this->save();

        return $this;
    }

    /**
     * Revoke multiple permissions at once.
     */
    public function revokePermissions(array $permissions): self
    {
        foreach ($permissions as $permission) {
            if (!$this->permissions) {
                continue;
            }
            $perms = $this->permissions;
            unset($perms[$permission]);
            $this->permissions = $perms;
        }

        $this->save();

        return $this;
    }

    /**
     * Get all permissions that are granted (set to true).
     */
    public function getGrantedPermissions(): array
    {
        if (!$this->permissions) {
            return [];
        }

        return array_keys(
            array_filter($this->permissions, fn($value) => $value === true)
        );
    }

    /**
     * Check if the admin role has all of the given permissions.
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the admin role has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }
}
