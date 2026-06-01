<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdminRole>
 */
class AdminRoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'permissions' => null,
        ];
    }

    /**
     * Give the admin role a specific set of permissions.
     */
    public function withPermissions(array $permissions): static
    {
        return $this->state(function () use ($permissions) {
            $permissionsArray = [];
            foreach ($permissions as $permission) {
                $permissionsArray[$permission] = true;
            }

            return [
                'permissions' => $permissionsArray,
            ];
        });
    }

    /**
     * Give the admin role all available permissions.
     */
    public function withAllPermissions(): static
    {
        return $this->withPermissions(\App\Models\AdminRole::availablePermissions());
    }

    /**
     * Create an admin role without any permissions.
     */
    public function withoutPermissions(): static
    {
        return $this->state(function () {
            return [
                'permissions' => null,
            ];
        });
    }
}
