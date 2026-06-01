<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

/**
 * Admin User Controller
 *
 * Template controller for managing users from the admin panel.
 * Copy this pattern for other admin resources.
 */
class UserController extends Controller
{
    /**
     * List all users.
     * Requires: user:view permission
     */
    public function index(AdminRequest $request): JsonResponse
    {
        // Check permission
        $request->permission('user:view');

        $users = User::paginate(15);

        return response()->json($users);
    }

    /**
     * Get a specific user.
     * Requires: user:view permission
     */
    public function show(AdminRequest $request, User $user): JsonResponse
    {
        // Check permission
        $request->permission('user:view');

        return response()->json($user);
    }

    /**
     * Create a new user.
     * Requires: user:create permission
     */
    public function store(AdminRequest $request): JsonResponse
    {
        // Check permission
        $request->permission('user:create');

        // Validate input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        return response()->json($user, 201);
    }

    /**
     * Update a user.
     * Requires: user:update permission
     */
    public function update(AdminRequest $request, User $user): JsonResponse
    {
        // Check permission
        $request->permission('user:update');

        // Validate input
        $validated = $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
        ]);

        // Update user
        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $user->update($validated);

        return response()->json($user);
    }

    /**
     * Delete a user.
     * Requires: user:delete permission
     */
    public function destroy(AdminRequest $request, User $user): JsonResponse
    {
        // Check permission
        $request->permission('user:delete');

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
