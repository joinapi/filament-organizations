<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Joinapi\FilamentOrganizations\FilamentOrganizations;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'profile_photo_path' => null,
            'current_organization_id' => null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user should have a personal organization.
     */
    public function withPersonalOrganization(?callable $callback = null): static
    {
        if (! FilamentOrganizations::hasOrganizationFeatures()) {
            return $this->state([]);
        }

        return $this->has(
            Organization::factory()
                ->state(function (array $attributes, User $user) {
                    return ['name' => $user->name . ' Organization', 'user_id' => $user->id, 'personal_organization' => true];
                }),
            'ownedOrganizations'
        );
    }
}
