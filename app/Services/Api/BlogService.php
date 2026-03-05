<?php

namespace App\Services\Api;

use App\Services\Api\Clients\BaseApiClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BlogService extends BaseApiClient
{
    protected int $cacheTtl;
    protected string $cacheKey = 'api.blogs.list';

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->cacheTtl = (int)($config['cache_ttl'] ?? 300);
    }

    /**
     * Get blog posts (with caching).
     *
     * @param bool $forceRefresh
     * @return array|null
     */
    public function getBlogs(bool $forceRefresh = false): ?array
    {
        if ($forceRefresh) {
            Cache::forget($this->cacheKey);
        }
        return Cache::remember($this->cacheKey, $this->cacheTtl, function () {
            $result = $this->request('get', 'blog-posts');
            // Expecting paginated response: data, current_page, last_page, etc.
            if (!is_array($result) || !isset($result['data']) || !is_array($result['data'])) {
                Log::warning('[API] Unexpected blog list response', ['result' => $result]);
                return null;
            }
            // Normalize for frontend
            return array_map(function ($item) {
                return [
                    'id' => $item['id'],
                    'title' => $item['title'] ?? '',
                    'image' => $item['featured_image_url'] ?? null,
                    'excerpt' => $item['excerpt'] ?? '',
                    'author' => isset($item['author']) ? trim(($item['author']['first_name'] ?? '') . ' ' . ($item['author']['last_name'] ?? '')) : '',
                    'published_at' => $item['published_at'] ?? '',
                    'slug' => $item['slug'] ?? '',
                    'category' => isset($item['category']) ? [
                        'id' => $item['category']['id'] ?? null,
                        'name' => $item['category']['name'] ?? '',
                        'slug' => $item['category']['slug'] ?? '',
                    ] : null,
                ];
            }, $result['data']);
        });
    }

    public function invalidateCache(): void
    {
        Cache::forget($this->cacheKey);
    }
}
