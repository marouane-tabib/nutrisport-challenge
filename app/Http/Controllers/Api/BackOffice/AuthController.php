<?php

namespace App\Http\Controllers\Api\BackOffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\BackOffice\LoginRequest;
use App\Http\Resources\AgentResource;
use App\Services\AgentAuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        private AgentAuthService $agentAuthService,
    ) {}

    /**
     * Login an agent.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->agentAuthService->login($request->validated());
            $result['agent'] = new AgentResource($result['agent']);

            return successResponse($result);
        } catch (AuthenticationException $e) {
            return errorResponse($e->getMessage(), 401);
        }
    }

    /**
     * Logout the agent.
     */
    public function logout(): JsonResponse
    {
        $this->agentAuthService->logout();
        
        return successResponse(null, 'Successfully logged out');
    }

    /**
     * Refresh the JWT token.
     */
    public function refresh(): JsonResponse
    {
        return successResponse($this->agentAuthService->refresh());
    }
}
