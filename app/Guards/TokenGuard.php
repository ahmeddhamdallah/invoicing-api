<?php

namespace App\Guards;

use App\Models\ApiToken;
use App\Models\Customer;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class TokenGuard implements Guard
{
    use GuardHelpers;

    protected $request;
    protected $inputKey;
    protected $storageKey;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->inputKey = 'api_token';
        $this->storageKey = 'token';
    }

    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        $token = $this->getTokenForRequest();

        if (!empty($token)) {
            $apiToken = ApiToken::where($this->storageKey, $token)->first();
            
            if ($apiToken && $apiToken->isValid()) {
                $apiToken->update(['last_used_at' => now()]);
                $this->user = $apiToken->customer;
                return $this->user;
            }
        }

        return null;
    }

    public function validate(array $credentials = [])
    {
        if (empty($credentials[$this->inputKey])) {
            return false;
        }

        $token = $credentials[$this->inputKey];
        $apiToken = ApiToken::where($this->storageKey, $token)->first();
        
        return $apiToken && $apiToken->isValid();
    }

    protected function getTokenForRequest()
    {
        $token = $this->request->bearerToken();

        if (empty($token)) {
            $token = $this->request->input($this->inputKey);
        }

        return $token;
    }
}
