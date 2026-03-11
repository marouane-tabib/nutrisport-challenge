<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\LoginRequest;
use App\Http\Requests\Client\RegisterRequest;
use App\Http\Requests\Client\UpdatePasswordRequest;
use App\Http\Requests\Client\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService,
    ) {}

    /**
     * Register a new client user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated(), $request->site->id);
        $result['user'] = new UserResource($result['user']);

        return successResponse($result, 'Registered successfully', 201);
    }

    /**
     * Login a client user.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->validated(), $request->site->id);
            $result['user'] = new UserResource($result['user']);

            return successResponse($result);
        } catch (AuthenticationException $e) {
            return errorResponse($e->getMessage(), 401);
        }
    }

    /**
     * Logout the authenticated user.
     */
    public function logout(): JsonResponse
    {
        $this->authService->logout();
        
        return successResponse(null, 'Successfully logged out');
    }

    /**
     * Refresh the JWT token.
     */
    public function refresh(): JsonResponse
    {
        return successResponse($this->authService->refresh());
    }

    /**
     * Get the authenticated user's profile.
     */
    public function profile(): JsonResponse
    {
        return successResponse(new UserResource($this->authService->getProfile()));
    }

    /**
     * Update the authenticated user's profile.
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $updated = $this->authService->updateProfile($request->validated());

        return successResponse(new UserResource($updated));
    }

    /**
     * Update the authenticated user's password.
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        try {
            $this->authService->updatePassword($request->current_password, $request->password);

            return successResponse(null, 'Password updated successfully');
        } catch (ValidationException $e) {
            return errorResponse($e->getMessage(), 422, $e->errors());
        }
    }
}
