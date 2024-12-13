<?php

namespace App\Console\Commands;

use App\Models\ApiToken;
use App\Models\Customer;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateApiToken extends Command
{
    protected $signature = 'api:token:create {--customer= : The ID of the customer} {--name= : The name of the token}';
    protected $description = 'Create a new API token for a customer';

    public function handle()
    {
        $customerId = $this->option('customer');
        $name = $this->option('name') ?? 'API Token';

        if (!$customerId) {
            $this->error('Please provide a customer ID using --customer option');
            return 1;
        }

        $customer = Customer::find($customerId);
        if (!$customer) {
            $this->error("Customer with ID {$customerId} not found");
            return 1;
        }

        $token = ApiToken::create([
            'customer_id' => $customer->id,
            'name' => $name,
            'token' => Str::random(60),
        ]);

        $this->info('API Token created successfully:');
        $this->line('Token: ' . $token->token);
        return 0;
    }
} 