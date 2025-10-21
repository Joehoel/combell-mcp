<?php

namespace App\Mcp\Traits;

use Saloon\Http\Response;

trait PaginationTrait
{
    /**
     * Generic paginated request to the Combell API.
     * Automatically handles pagination using skip/take parameters.
     *
     * @param callable $apiCall A callable that makes the API request with skip and take parameters
     * @param int $pageSize Number of items per page (default: 100)
     * @return array All results combined from all pages
     */
    protected function paginate(
        callable $apiCall,
        int $pageSize = 100
    ): array {
        $allResults = [];
        $skip = 0;
        $hasMore = true;

        while ($hasMore) {
            $response = $apiCall($skip, $pageSize);

            if (!$response instanceof Response) {
                break;
            }

            $data = $response->json();

            if (!is_array($data) || empty($data)) {
                $hasMore = false;
            } else {
                $allResults = array_merge($allResults, $data);

                if (count($data) < $pageSize) {
                    $hasMore = false;
                } else {
                    $skip += $pageSize;
                }
            }
        }

        return $allResults;
    }
}
