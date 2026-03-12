<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

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

if (!function_exists('formatPagination')) {
    /**
     * Format paginated results into a structured array.
     *
     * Transforms a LengthAwarePaginator instance into an array containing
     * the paginated data items and metadata (total count, per page, current page, last page).
     *
     * @param  LengthAwarePaginator $paginator The paginated results to format.
     * @return array An array with 'data' and 'meta' keys containing pagination information.
     */
    function formatPagination(LengthAwarePaginator $paginator): array
    {
        return [
            'data'       => $paginator->items(),
            'meta'       => [
                'total'    => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'page'     => $paginator->currentPage(),
                'count'    => $paginator->lastPage(),
            ],
        ];
    }
}

if (!function_exists('formatPaginatedResource')) {
    /**
     * Format paginated resource collection for API response.
     *
     * Transforms items through a resource and preserves pagination metadata.
     *
     * @param  LengthAwarePaginator $paginator The paginated results.
     * @param  string $resourceClass The resource class to transform items.
     * @return array An array with 'data' and 'meta' keys.
     */
    function formatPaginatedResource(LengthAwarePaginator $paginator, string $resourceClass): array
    {
        return [
            'data' => $resourceClass::collection($paginator->items()),
            'meta' => [
                'total'    => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'page'     => $paginator->currentPage(),
                'count'    => $paginator->lastPage(),
            ],
        ];
    }
}
