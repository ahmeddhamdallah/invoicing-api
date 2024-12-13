<?php

namespace Tests\Unit\Guards;

use App\Guards\TokenGuard;
use App\Models\ApiToken;
use App\Models\Customer;
use Illuminate\Http\Request;
use Tests\TestCase;

class TokenGuardTest extends TestCase
{
    private TokenGuard $guard;
    private Request $request;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->request = new Request();
        $this->guard = new TokenGuard($this->request);
    }

    public function testValidateToken()
    {
        $customer = Customer::factory()->create();
        $token = ApiToken::factory()->create([
            'customer_id' => $customer->id,
            'token' => 'valid_token',
            'expires_at' => null
        ]);

        $this->request->headers->set('Authorization', 'Bearer valid_token');

        $isValid = $this->guard->validate(['api_token' => 'valid_token']);

        $this->assertTrue($isValid);
        $this->assertDatabaseHas('api_tokens', [
            'id' => $token->id,
            'token' => 'valid_token',
            'customer_id' => $customer->id
        ]);
    }

    public function testInvalidateToken()
    {
        $this->request->headers->set('Authorization', 'Bearer invalid_token');

        $isValid = $this->guard->validate(['api_token' => 'invalid_token']);

        $this->assertFalse($isValid);
    }

    public function testExpiredTokenIsInvalid()
    {
        $customer = Customer::factory()->create();
        $token = ApiToken::factory()->create([
            'customer_id' => $customer->id,
            'token' => 'expired_token',
            'expires_at' => now()->subDay() 
        ]);

        $this->request->headers->set('Authorization', 'Bearer expired_token');

        $isValid = $this->guard->validate(['api_token' => 'expired_token']);

        $this->assertFalse($isValid);
    }
} 