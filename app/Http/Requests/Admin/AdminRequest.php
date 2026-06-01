<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Base Admin Request Class
 *
 * All admin API requests should extend this class to ensure:
 * 1. User is authenticated
 * 2. User has an AdminRole
 * 3. Permission checking with custom permission() method
 */
class AdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Only allows users who have an AdminRole assigned.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Check if the user has a specific admin permission.
     *
     * Throws UnauthorizedException if permission is not granted.
     * Use this in your controller methods to verify permissions.
     *
     * @param string $permission The permission key (e.g., 'user:create')
     * @return void
     * @throws \App\Exceptions\UnauthorizedException
     */
    public function permission(string $permission): void
    {
        $this->user()->requireAdminPermission($permission);
    }

    /**
     * Check if the user has a specific admin permission.
     *
     * Returns boolean instead of throwing exception.
     * Useful for conditional logic rather than authorization.
     */
    public function hasPermission(string $permission): bool
    {
        $adminRole = $this->user()?->adminRole;
        return $adminRole && $adminRole->hasPermission($permission);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [];
    }
}
