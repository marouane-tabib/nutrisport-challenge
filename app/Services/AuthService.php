<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\JWTGuard;

class AuthService extends BaseService
{
    private JWTGuard $auth;

    public function __construct()
    {
        $this->auth = auth('client');
        $this->auth->factory()->setTTL(config('jwt.client_ttl', 360));
    }

    /**
     * Register a new user and return user + JWT token.
     *
     * @throws AuthenticationException
     */
    public function register(array $data, int $siteId): array
    {
        $user = User::create([
            'site_id' => $siteId,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $token = $this->auth->fromUser($user);

        return [
            'user' => $user,
            'token' => $token,
            'expires_in' => $this->getExpiresIn(),
        ];
    }

    /**
     * Authenticate a user by email + password scoped to a site.
     *
     * @throws AuthenticationException
     */
    public function login(array $credentials, int $siteId): array
    {
        $user = User::where('email', $credentials['email'])
            ->where('site_id', $siteId)
            ->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new AuthenticationException('Invalid credentials');
        }

        $token = $this->auth->fromUser($user);

        return [
            'user' => $user,
            'token' => $token,
            'expires_in' => $this->getExpiresIn(),
        ];
    }

    /**
     * Invalidate the current token.
     */
    public function logout(): void
    {
        $this->auth->logout();
    }

    /**
     * Refresh the current token.
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
     * Get the authenticated user.
     */
    public function getProfile(): User
    {
        return $this->auth->user();
    }

    /**
     * Update user profile fields.
     */
    public function updateProfile(array $data): User
    {
        $user = $this->auth->user();
        $user->update($data);
        
        return $user->fresh();
    }

    /**
     * Update user password after verifying current password.
     *
     * @throws ValidationException
     */
    public function updatePassword(string $currentPassword, string $newPassword): void
    {
        $user = $this->auth->user();
        
        if (!Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Current password is incorrect'],
            ]);
        }

        $user->update(['password' => $newPassword]);
    }

    /**
     * Get expires_in value in seconds.
     */
    private function getExpiresIn(): int
    {
        return config('jwt.client_ttl', 360) * 60;
    }
}
