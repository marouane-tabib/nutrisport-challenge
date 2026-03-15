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
     * Register a new user and return user data with JWT token.
     *
     * @param array $data The user registration data (first_name, last_name, email, password)
     * @param int $siteId The site ID for the user
     * @return array The response containing user, token, and expires_in
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
     * Authenticate a user by email and password scoped to a specific site.
     *
     * @param array $credentials The login credentials (email, password)
     * @param int $siteId The site ID to scope the authentication
     * @return array The response containing user, token, and expires_in
     * @throws AuthenticationException When credentials are invalid
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
     * Invalidate the current JWT token and logout the user.
     *
     * @return void
     */
    public function logout(): void
    {
        $this->auth->logout();
    }

    /**
     * Refresh the current JWT token for the authenticated user.
     *
     * @return array The response containing the new token and expires_in
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
     * Get the authenticated user's profile information.
     *
     * @return User The authenticated user model
     */
    public function getProfile(): User
    {
        return $this->auth->user();
    }

    /**
     * Update the authenticated user's profile fields.
     *
     * @param array $data The profile data to update (first_name, last_name, email)
     * @return User The updated user model
     */
    public function updateProfile(array $data): User
    {
        $user = $this->auth->user();
        $user->update($data);
        
        return $user->fresh();
    }

    /**
     * Update the authenticated user's password after verifying the current password.
     *
     * @param string $currentPassword The user's current password
     * @param string $newPassword The new password to set
     * @return void
     * @throws ValidationException When current password is incorrect
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
     * Get the JWT token expiration time in seconds.
     *
     * @return int The expiration time in seconds
     */
    private function getExpiresIn(): int
    {
        return config('jwt.client_ttl', 360) * 60;
    }
}
