<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceEventFactory extends Factory
{
    public function definition()
    {
        $types = ['registration', 'activation', 'appointment'];
        $prices = [50, 100, 200];
        $typeIndex = $this->faker->numberBetween(0, 2);

        return [
            'invoice_id' => Invoice::factory(),
            'user_id' => User::factory(),
            'type' => $types[$typeIndex],
            'date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'price' => $prices[$typeIndex]
        ];
    }
}
