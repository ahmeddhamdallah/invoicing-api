<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 year', 'now');
        return [
            'customer_id' => Customer::factory(),
            'start_date' => $startDate,
            'end_date' => $this->faker->dateTimeBetween($startDate, '+1 month'),
            'total_amount' => $this->faker->randomFloat(2, 50, 1000),
        ];
    }
}
