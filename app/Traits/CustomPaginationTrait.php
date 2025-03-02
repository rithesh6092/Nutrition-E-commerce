<?php

namespace App\Traits;

/**
 * @OA\Schema(
 *     schema="PaginationResource",
 *     title="PaginationResource",
 *     description="Pagination meta info resource",
 *     @OA\Property(property="total", type="integer", example=100),
 *     @OA\Property(property="per_page", type="integer", example=10),
 *     @OA\Property(property="current_page", type="integer", example=1),
 *     @OA\Property(property="last_page", type="integer", example=10),
 *     @OA\Property(property="next_page_url", type="string", nullable=true, example="http://example.com/api/products?page=2"),
 *     @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
 *     @OA\Property(
 *         property="url",
 *         type="object",
 *         @OA\Property(property="path", type="string", example="http://example.com/api/products"),
 *         @OA\Property(property="pageName", type="string", example="products")
 *     )
 * )
 */
trait CustomPaginationTrait
{
    public static function buildPagination($paginator, $resourceName = null)
    {
        $options = $paginator->getOptions();
        
        // Get the resource name from the URL path
        if (!$resourceName) {
            $path = $options['path'];
            $segments = explode('/', trim($path, '/'));
            $resourceName = end($segments);
        }

        return [
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'next_page_url' => $paginator->nextPageUrl(),
            'prev_page_url' => $paginator->previousPageUrl(),
            'url' => [
                'path' => $options['path'],
                'pageName' => $resourceName
            ],
        ];
    }
} 