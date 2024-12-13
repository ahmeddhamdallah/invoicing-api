<?php

namespace Database\Factories;

use App\Models\ApiToken;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApiToken>
 */
class ApiTokenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ApiToken::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'token' => $this->faker->uuid(),
            'name' => $this->faker->word(),
            'last_used_at' => null,
            'expires_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 