<?php

namespace Database\Seeders;

use App\Models\ApiToken;
use App\Models\Customer;
use App\Models\User;
use App\Models\UserSession;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        $customer = Customer::create([
            'name' => 'Example Corp',
            'email' => 'billing@example.com'
        ]);

        $token = ApiToken::create([
            'customer_id' => $customer->id,
            'name' => 'Test Token',
            'token' => Str::random(60)
        ]);

        $userA = User::create([
            'customer_id' => $customer->id,
            'name' => 'User A',
            'email' => 'usera@example.com',
            'registered_at' => '2020-12-01 10:00:00'
        ]);

        UserSession::create([
            'user_id' => $userA->id,
            'activated_at' => '2021-01-15 10:00:00',
            'appointment_at' => null
        ]);

        UserSession::create([
            'user_id' => $userA->id,
            'activated_at' => '2021-01-18 10:00:00',
            'appointment_at' => null
        ]);

        $userB = User::create([
            'customer_id' => $customer->id,
            'name' => 'User B',
            'email' => 'userb@example.com',
            'registered_at' => '2020-12-15 10:00:00'
        ]);

        UserSession::create([
            'user_id' => $userB->id,
            'activated_at' => null,
            'appointment_at' => '2021-01-15 10:00:00'
        ]);

        $userC = User::create([
            'customer_id' => $customer->id,
            'name' => 'User C',
            'email' => 'userc@example.com',
            'registered_at' => '2021-01-01 10:00:00'
        ]);

        UserSession::create([
            'user_id' => $userC->id,
            'activated_at' => '2021-01-10 10:00:00',
            'appointment_at' => null
        ]);

        $userD = User::create([
            'customer_id' => $customer->id,
            'name' => 'User D',
            'email' => 'userd@example.com',
            'registered_at' => '2020-09-01 10:00:00'
        ]);

        UserSession::create([
            'user_id' => $userD->id,
            'activated_at' => '2020-10-11 10:00:00',
            'appointment_at' => null
        ]);

        UserSession::create([
            'user_id' => $userD->id,
            'activated_at' => '2020-12-12 10:00:00',
            'appointment_at' => null
        ]);

        UserSession::create([
            'user_id' => $userD->id,
            'activated_at' => null,
            'appointment_at' => '2020-12-27 10:00:00'
        ]);

        $this->command->info('Test data seeded successfully!');
        $this->command->info('Your API token is: ' . $token->token);
        $this->command->info('Customer ID: ' . $customer->id);
        $this->command->info("\nTest scenarios for period 2021-01-01 to 2021-02-01:");
        $this->command->info("User A (ID: {$userA->id}): Should be charged 50 SAR");
        $this->command->info("  - Registered before period (2020-12-01)");
        $this->command->info("  - Activated in period (2021-01-15, 2021-01-18)");
        $this->command->info("\nUser B (ID: {$userB->id}): Should be charged 200 SAR");
        $this->command->info("  - Registered before period (2020-12-15)");
        $this->command->info("  - Made appointment in period (2021-01-15)");
        $this->command->info("\nUser C (ID: {$userC->id}): Should be charged 100 SAR");
        $this->command->info("  - Registered in period (2021-01-01)");
        $this->command->info("  - Activated in period (2021-01-10)");
        $this->command->info("\nUser D (ID: {$userD->id}): Should NOT be charged");
        $this->command->info("  - All events before period");
        $this->command->info("  - Registration (2020-09-01)");
        $this->command->info("  - Activations (2020-10-11, 2020-12-12)");
        $this->command->info("  - Appointment (2020-12-27)");
        $this->command->info("\nExpected total invoice amount: 350 SAR");
    }
}
