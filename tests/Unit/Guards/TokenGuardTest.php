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
        
        // Create a fresh request for each test
        $this->request = new Request();
        $this->guard = new TokenGuard($this->request);
    }

    public function testValidateToken()
    {
        // Arrange
        $customer = Customer::factory()->create();
        $token = ApiToken::factory()->create([
            'customer_id' => $customer->id,
            'token' => 'valid_token',
            'expires_at' => null
        ]);

        $this->request->headers->set('Authorization', 'Bearer valid_token');

        // Act
        $isValid = $this->guard->validate(['api_token' => 'valid_token']);

        // Assert
        $this->assertTrue($isValid);
        $this->assertDatabaseHas('api_tokens', [
            'id' => $token->id,
            'token' => 'valid_token',
            'customer_id' => $customer->id
        ]);
    }

    public function testInvalidateToken()
    {
        // Arrange
        $this->request->headers->set('Authorization', 'Bearer invalid_token');

        // Act
        $isValid = $this->guard->validate(['api_token' => 'invalid_token']);

        // Assert
        $this->assertFalse($isValid);
    }

    public function testExpiredTokenIsInvalid()
    {
        // Arrange
        $customer = Customer::factory()->create();
        $token = ApiToken::factory()->create([
            'customer_id' => $customer->id,
            'token' => 'expired_token',
            'expires_at' => now()->subDay() // Token expired yesterday
        ]);

        $this->request->headers->set('Authorization', 'Bearer expired_token');

        // Act
        $isValid = $this->guard->validate(['api_token' => 'expired_token']);

        // Assert
        $this->assertFalse($isValid);
    }
} 