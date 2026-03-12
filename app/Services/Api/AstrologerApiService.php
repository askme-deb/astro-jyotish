<?php

namespace App\Services\Api;

use App\Services\Api\Clients\BaseApiClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * AstrologerApiService
 * Handles all astrologer-related API operations.
 * Business logic, caching, and error handling are managed here.
 */
class AstrologerApiService extends BaseApiClient
{
    protected int $cacheTtl;
    protected string $cacheKey = 'api.astrologers.list';

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->cacheTtl = (int)($config['cache_ttl'] ?? 300);
    }

    /**
     * Get astrologer list (with caching).
     *
     * @param bool $forceRefresh
     * @return array|null
     */
    public function getAstrologers(bool $forceRefresh = false): ?array
    {
        if ($forceRefresh) {
            Cache::forget($this->cacheKey);
        }
        return Cache::remember($this->cacheKey, $this->cacheTtl, function () {
            $result = $this->request('get', 'astrologers');
            if (!is_array($result) || !isset($result['status']) || !$result['status'] || !isset($result['data']) || !is_array($result['data'])) {
                Log::warning('[API] Unexpected astrologer list response', ['result' => $result]);
                return null;
            }
            // Normalize for frontend
            return array_map(function ($item) {
                return [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'price' => $item['rate'],
                    'duration' => $item['duration'],
                    'image' => $item['image_url'] ?? null,
                    'experience' => $item['experience'] . ' Years',
                    'rating' => $item['rating'] ?? '-',
                    'reviews' => $item['reviews_count'] ?? 0,
                    'skills' => $item['skills'] ?? [],
                    'languages' => isset($item['languages']) ? implode(', ', $item['languages']) : '',
                    'slug' => \Str::slug($item['name']),
                ];
            }, $result['data']);
        });
    }


        /**
     * Get a single astrologer by ID or slug.
     *
     * @param string|int $idOrSlug
     * @return array|null
     */
    public function getAstrologerByIdOrSlug($idOrSlug): ?array
    {
        $result = $this->request('get', "astrologers/{$idOrSlug}");
        if (!is_array($result) || !isset($result['status']) || !$result['status'] || !isset($result['data']) || !is_array($result['data'])) {
            Log::warning('[API] Unexpected astrologer profile response', ['result' => $result]);
            return null;
        }
        // Normalize for frontend (adjust keys as needed)
        $item = $result['data'];
        return [
            'id' => $item['id'],
            'name' => $item['name'],
            'price' => $item['rate'],
            'duration' => $item['duration'],
            'image' => $item['image_url'] ?? null,
            'experience' => $item['experience'] . ' Years',
            'rating' => $item['rating'] ?? '-',
            'reviews' => $item['reviews_count'] ?? 0,
            'skills' => $item['skills'] ?? [],
            'languages' => isset($item['languages']) ? implode(', ', $item['languages']) : '',
            'slug' => \Str::slug($item['name']),
            'bio' => $item['bio'] ?? '',
            // Add more fields as needed
        ];
    }
    /**
     * Invalidate astrologer cache (for queue/background refresh).
     */
    public function invalidateCache(): void
    {
        Cache::forget($this->cacheKey);
    }
}
