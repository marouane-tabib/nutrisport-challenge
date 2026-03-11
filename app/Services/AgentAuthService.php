<?php

namespace App\Services;

use Illuminate\Auth\AuthenticationException;
use PHPOpenSourceSaver\JWTAuth\JWTGuard;

class AgentAuthService extends BaseService
{
    private JWTGuard $auth;

    public function __construct()
    {
        $this->auth = auth('agent');
        $this->auth->factory()->setTTL(config('jwt.agent_ttl', 480));
    }

    /**
     * Authenticate an agent by email + password.
     *
     * @throws AuthenticationException
     */
    public function login(array $credentials): array
    {
        $token = $this->auth->attempt($credentials);

        if (!$token) {
            throw new AuthenticationException('Invalid credentials --');
        }

        $agent = $this->auth->user();

        return [
            'agent' => $agent,
            'token' => $token,
            'expires_in' => $this->getExpiresIn(),
        ];
    }

    /**
     * Invalidate the current agent token.
     */
    public function logout(): void
    {
        $this->auth->logout();
    }

    /**
     * Refresh the current agent token.
     */
    public function refresh(): array
    {
        $token = $this->auth->refresh();

        return [
            'token' => $token,
            'expires_in' => $this->getExpiresIn(),
        ];
    }

    /**
     * Get expires_in value in seconds.
     */
    private function getExpiresIn(): int
    {
        return config('jwt.agent_ttl', 480) * 60;
    }
}
