<?php

namespace App\Http\Controllers\Api\BackOffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\BackOffice\LoginRequest;
use App\Http\Resources\AgentResource;
use App\Services\AgentAuthService;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        private AgentAuthService $agentAuthService,
    ) {}

    /**
     * Login an agent with credentials.
     *
     * @param LoginRequest $request The login request containing agent credentials
     * @return JsonResponse The response containing the agent data and JWT token
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->agentAuthService->login($request->validated());
            $result['agent'] = new AgentResource($result['agent']);

            return successResponse($result);
        } catch (AuthenticationException $e) {
            return errorResponse($e->getMessage(), 401);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Logout the authenticated agent and invalidate the JWT token.
     *
     * @return JsonResponse The logout confirmation response
     */
    public function logout(): JsonResponse
    {
        try {
            $this->agentAuthService->logout();
            
            return successResponse(null, 'Successfully logged out');
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Refresh the JWT token for the authenticated agent.
     *
     * @return JsonResponse The response containing the new JWT token
     */
    public function refresh(): JsonResponse
    {
        try {
            return successResponse($this->agentAuthService->refresh());
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }
}
