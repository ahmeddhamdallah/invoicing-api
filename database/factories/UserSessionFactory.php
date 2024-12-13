<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserSession;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserSessionFactory extends Factory
{
    protected $model = UserSession::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'activated_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'appointment_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
