<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\LoginRequest;
use App\Http\Requests\Client\RegisterRequest;
use App\Http\Requests\Client\UpdatePasswordRequest;
use App\Http\Requests\Client\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService,
    ) {}

    /**
     * Register a new client user with email and password.
     *
     * @param RegisterRequest $request The registration request containing user credentials
     * @return JsonResponse The response containing the registered user data and JWT token
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->register($request->validated(), $request->site->id);
            $result['user'] = new UserResource($result['user']);

            return successResponse($result, 'Registered successfully', 201);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Login a client user with email and password.
     *
     * @param LoginRequest $request The login request containing user credentials
     * @return JsonResponse The response containing the user data and JWT token
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->validated(), $request->site->id);
            $result['user'] = new UserResource($result['user']);

            return successResponse($result);
        } catch (AuthenticationException $e) {
            return errorResponse($e->getMessage(), 401);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Logout the authenticated user and invalidate the JWT token.
     *
     * @return JsonResponse The logout confirmation response
     */
    public function logout(): JsonResponse
    {
        try {
            $this->authService->logout();
            
            return successResponse(null, 'Successfully logged out');
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Refresh the JWT token for the authenticated user.
     *
     * @return JsonResponse The response containing the new JWT token
     */
    public function refresh(): JsonResponse
    {
        try {
            return successResponse($this->authService->refresh());
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get the authenticated user's profile information.
     *
     * @return JsonResponse The response containing the user profile data
     */
    public function profile(): JsonResponse
    {
        try {
            return successResponse(new UserResource($this->authService->getProfile()));
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Update the authenticated user's profile information.
     *
     * @param UpdateProfileRequest $request The request containing updated profile data
     * @return JsonResponse The response containing the updated user profile
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $updated = $this->authService->updateProfile($request->validated());

            return successResponse(new UserResource($updated));
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Update the authenticated user's password with validation.
     *
     * @param UpdatePasswordRequest $request The request containing current and new password
     * @return JsonResponse The response confirming password update
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        try {
            $this->authService->updatePassword($request->current_password, $request->password);

            return successResponse(null, 'Password updated successfully');
        } catch (ValidationException $e) {
            return errorResponse($e->getMessage(), 422, $e->errors());
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }
}
