<?php

use Illuminate\Http\JsonResponse;

if (!function_exists('successResponse')) {
    /**
     * Return a success response.
     */
    function successResponse(mixed $data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}

if (!function_exists('errorResponse')) {
    /**
     * Return an error response.
     */
    function errorResponse(string $message = 'Error', int $code = 400, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $errors,
        ], $code);
    }
}
