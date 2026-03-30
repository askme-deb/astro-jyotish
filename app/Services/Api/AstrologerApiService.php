<?php

namespace App\Services\Api;

use App\Services\Api\Clients\BaseApiClient;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * AstrologerApiService
 * Handles all astrologer-related API operations.
 * Business logic, caching, and error handling are managed here.
 */
class AstrologerApiService extends BaseApiClient
{
    /**
     * Create a new astrologer (register) via external API.
     *
     * @param array $data
     * @return array|null
     */
    public function createAstrologer(array $data): ?array
    {
        // Use Laravel's Http::attach() for all files and arrays
        $http = \Illuminate\Support\Facades\Http::baseUrl($this->baseUrl)
            ->timeout($this->timeoutSeconds)
            ->retry($this->retryTimes, $this->retrySleepMilliseconds, function ($exception) {
                return $exception instanceof \Illuminate\Http\Client\ConnectionException;
            });
        if ($this->token !== null && $this->token !== '') {
            $http = $http->withToken($this->token);
        }
        // Attach files
        foreach (['photo', 'aadhar_document', 'pan_document', 'signature'] as $fileField) {
            if (isset($data[$fileField]) && $data[$fileField] instanceof \Illuminate\Http\UploadedFile) {
                $http = $http->attach($fileField, fopen($data[$fileField]->getRealPath(), 'r'), $data[$fileField]->getClientOriginalName());
            }
        }
        // Attach education documents
        if (!empty($data['education'])) {
            foreach ($data['education'] as $i => $edu) {
                if (isset($edu['document']) && $edu['document'] instanceof \Illuminate\Http\UploadedFile) {
                    $http = $http->attach("education[$i][document]", fopen($edu['document']->getRealPath(), 'r'), $edu['document']->getClientOriginalName());
                }
            }
        }
        // Build form fields
        $fields = [];
        foreach ($data as $key => $value) {
            if (in_array($key, ['photo', 'aadhar_document', 'pan_document', 'signature', 'education', 'availabilities', 'languages', 'skills'])) continue;
            $fields[$key] = $value ?? '';
        }
        // Arrays (languages, skills)
        if (!empty($data['languages'])) {
            foreach ($data['languages'] as $lang) {
                $fields['languages[]'][] = $lang;
            }
        }
        if (!empty($data['skills'])) {
            foreach ($data['skills'] as $skill) {
                $fields['skills[]'][] = $skill;
            }
        }
        // Availabilities
        if (!empty($data['availabilities'])) {
            foreach ($data['availabilities'] as $i => $avail) {
                $fields["availabilities[$i][day]"] = $avail['day'] ?? '';
                if (!empty($avail['slots'])) {
                    foreach ($avail['slots'] as $j => $slot) {
                        $fields["availabilities[$i][slots][$j][from]"] = $slot['from'] ?? '';
                        $fields["availabilities[$i][slots][$j][to]"] = $slot['to'] ?? '';
                    }
                }
            }
        }
        // Education (non-file fields)
        if (!empty($data['education'])) {
            foreach ($data['education'] as $i => $edu) {
                $fields["education[$i][degree]"] = $edu['degree'] ?? '';
                $fields["education[$i][institution]"] = $edu['institution'] ?? '';
                $fields["education[$i][year]"] = $edu['year'] ?? '';
            }
        }

        // Log payload for debugging (sanitized, no file contents)
        $logPayload = $this->buildLogPayload($data, $fields);
        Log::info('[API] Astrologer registration payload', ['payload' => $logPayload]);

        $response = $http->asMultipart()->post('astrologers', $fields);
        $result = $response->json();
        $success = false;
        if (is_array($result)) {
            if ((isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success'])) {
                $success = true;
            }
        }
        if (!$success) {
            Log::warning('[API] Astrologer registration failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'result' => $result
            ]);
            return null;
        }
        // Prefer 'data', else fallback to 'astrologer' or whole result
        if (isset($result['data'])) {
            return $result['data'];
        } elseif (isset($result['astrologer'])) {
            return $result['astrologer'];
        } else {
            return $result;
        }
        if (!is_array($result) || !isset($result['status']) || !$result['status']) {
            Log::warning('[API] Astrologer registration failed', ['result' => $result]);
            return null;
        }
        return $result['data'] ?? null;
    }
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
            $normalized = $this->normalizeAstrologerListResponse($result, 'list');

            return $normalized['items'] !== [] ? $normalized['items'] : null;
        });
    }

    /**
     * Get astrologers from the external filter endpoint.
     *
     * @param array<string, mixed> $filters
     * @return array{items: array<int, array<string, mixed>>, meta: array<string, int>, query: array<string, mixed>}
     */
    public function filterAstrologers(array $filters = []): array
    {
        $query = $this->sanitizeFilterQuery($filters);
        $result = $this->request('get', 'astrologers/filter', [
            'query' => $query,
        ]);

        $normalized = $this->normalizeAstrologerListResponse($result, 'filter');

        return [
            'items' => $normalized['items'],
            'meta' => $normalized['meta'],
            'query' => $query,
        ];
    }

    /**
     * Build dynamic filter sections for the consultant page.
     *
     * @param array<int, array<string, mixed>>|null $astrologers
     * @return array<int, array<string, mixed>>
     */
    public function getConsultantFilterSections(?array $astrologers = null): array
    {
        $astrologers = is_array($astrologers) ? $astrologers : [];

        return [
            [
                'key' => 'expertise',
                'label' => 'Expertise',
                'single_select' => false,
                'options' => array_map(fn (string $value) => [
                    'label' => $value,
                    'value' => $value,
                ], $this->extractUniqueValues($astrologers, 'skills')),
            ],
            [
                'key' => 'languages',
                'label' => 'Languages',
                'single_select' => false,
                'options' => array_map(fn (string $value) => [
                    'label' => $value,
                    'value' => $value,
                ], $this->extractUniqueValues($astrologers, 'languages')),
            ],
            [
                'key' => 'rating',
                'label' => 'Rating',
                'single_select' => true,
                'options' => [
                    [
                        'label' => 'Highest Rated',
                        'value' => 'rating_desc',
                        'sort_by' => 'rating',
                        'sort_order' => 'desc',
                    ],
                    [
                        'label' => 'Lowest Rated',
                        'value' => 'rating_asc',
                        'sort_by' => 'rating',
                        'sort_order' => 'asc',
                    ],
                ],
            ],
            [
                'key' => 'experience',
                'label' => 'Experience',
                'single_select' => true,
                'options' => [
                    [
                        'label' => 'Most Experienced',
                        'value' => 'experience_desc',
                        'sort_by' => 'experience',
                        'sort_order' => 'desc',
                    ],
                    [
                        'label' => 'Least Experienced',
                        'value' => 'experience_asc',
                        'sort_by' => 'experience',
                        'sort_order' => 'asc',
                    ],
                ],
            ],
            [
                'key' => 'price',
                'label' => 'Price',
                'single_select' => true,
                'options' => [
                    [
                        'label' => 'Below ₹10/min',
                        'value' => 'price_below_10',
                        'min_rate' => 0,
                        'max_rate' => 10,
                    ],
                    [
                        'label' => '₹10 - ₹30/min',
                        'value' => 'price_10_30',
                        'min_rate' => 10,
                        'max_rate' => 30,
                    ],
                ],
            ],
        ];
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

        return $this->normalizeAstrologer($result['data']);
    }

    /**
     * Invalidate astrologer cache (for queue/background refresh).
     */
    public function invalidateCache(): void
    {
        Cache::forget($this->cacheKey);
    }

    /**
     * @param array<string, mixed> $result
     * @return array{items: array<int, array<string, mixed>>, meta: array<string, int>}
     */
    private function normalizeAstrologerListResponse(array $result, string $context): array
    {
        if (!isset($result['status']) || !$result['status']) {
            Log::warning('[API] Unexpected astrologer response status', [
                'context' => $context,
                'result' => $result,
            ]);

            return [
                'items' => [],
                'meta' => $this->defaultPaginationMeta(),
            ];
        }

        $payload = $result['data'] ?? [];
        $items = [];
        $metaSource = [];

        if (is_array($payload) && array_is_list($payload)) {
            $items = $payload;
            $metaSource = $result['pagination'] ?? $result;
        } elseif (is_array($payload) && isset($payload['data']) && is_array($payload['data'])) {
            $items = $payload['data'];
            $metaSource = $payload;
        }

        if (!is_array($items)) {
            Log::warning('[API] Unexpected astrologer list structure', [
                'context' => $context,
                'result' => $result,
            ]);

            return [
                'items' => [],
                'meta' => $this->defaultPaginationMeta(),
            ];
        }

        $normalizedItems = array_values(array_map(fn (array $item) => $this->normalizeAstrologer($item), $items));
        $meta = $this->normalizePaginationMeta($metaSource, count($normalizedItems));

        return [
            'items' => $normalizedItems,
            'meta' => $meta,
        ];
    }

    /**
     * @param array<string, mixed> $item
     * @return array<string, mixed>
     */
    private function normalizeAstrologer(array $item): array
    {
        $name = (string) ($item['name'] ?? 'Unknown');
        $skills = $this->sanitizeStringList($item['skills'] ?? $item['expertise'] ?? []);
        $languages = $this->sanitizeStringList($item['languages'] ?? []);
        $experience = $item['experience'] ?? null;
        $experienceValue = is_numeric($experience) ? (float) $experience : null;
        $experienceLabel = $experienceValue !== null
            ? rtrim(rtrim(number_format($experienceValue, 1, '.', ''), '0'), '.') . ' Years'
            : '';

        return [
            'id' => $item['id'] ?? null,
            'name' => $name,
            'price' => $item['rate'] ?? $item['price'] ?? null,
            'duration' => $item['duration'] ?? null,
            'image' => $item['image_url'] ?? $item['image'] ?? null,
            'experience' => $experienceLabel,
            'rating' => $item['rating'] ?? '-',
            'reviews' => $item['reviews_count'] ?? $item['reviews'] ?? 0,
            'skills' => $skills,
            'languages' => implode(', ', $languages),
            'slug' => Str::slug($name),
            'bio' => $item['bio'] ?? '',
        ];
    }

    /**
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    private function sanitizeFilterQuery(array $filters): array
    {
        $query = [
            'page' => max(1, (int) ($filters['page'] ?? 1)),
            'per_page' => min(50, max(1, (int) ($filters['per_page'] ?? 15))),
        ];

        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '') {
            $query['search'] = $search;
        }

        $languages = $this->sanitizeStringList($filters['languages'] ?? []);
        if ($languages !== []) {
            $query['languages'] = $languages;
        }

        $expertise = $this->sanitizeStringList($filters['expertise'] ?? []);
        if ($expertise !== []) {
            $query['expertise'] = $expertise;
        }

        $minRate = $filters['min_rate'] ?? null;
        if ($minRate !== null && $minRate !== '' && is_numeric($minRate)) {
            $query['min_rate'] = (int) $minRate;
        }

        $maxRate = $filters['max_rate'] ?? null;
        if ($maxRate !== null && $maxRate !== '' && is_numeric($maxRate)) {
            $query['max_rate'] = (int) $maxRate;
        }

        $sortBy = (string) ($filters['sort_by'] ?? '');
        if (in_array($sortBy, ['rating', 'experience', 'price', 'rate', 'name'], true)) {
            $query['sort_by'] = $sortBy;
            $query['sort_order'] = strtolower((string) ($filters['sort_order'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';
        }

        return $query;
    }

    /**
     * @param mixed $value
     * @return array<int, string>
     */
    private function sanitizeStringList(mixed $value): array
    {
        $values = is_array($value) ? $value : explode(',', (string) $value);
        $sanitized = [];

        foreach ($values as $entry) {
            $normalized = trim((string) $entry);
            if ($normalized !== '') {
                $sanitized[] = $normalized;
            }
        }

        return array_values(array_unique($sanitized));
    }

    /**
     * @param array<int, array<string, mixed>> $astrologers
     * @return array<int, string>
     */
    private function extractUniqueValues(array $astrologers, string $key): array
    {
        $values = [];

        foreach ($astrologers as $astrologer) {
            $entries = $this->sanitizeStringList($astrologer[$key] ?? []);
            foreach ($entries as $entry) {
                $values[] = $entry;
            }
        }

        $values = array_values(array_unique($values));
        natcasesort($values);

        return array_values($values);
    }

    /**
     * @param array<string, mixed> $source
     * @return array<string, int>
     */
    private function normalizePaginationMeta(array $source, int $count): array
    {
        $meta = [
            'current_page' => max(1, (int) ($source['current_page'] ?? 1)),
            'last_page' => max(1, (int) ($source['last_page'] ?? 1)),
            'per_page' => max(1, (int) ($source['per_page'] ?? ($count > 0 ? $count : 15))),
            'total' => max(0, (int) ($source['total'] ?? $count)),
        ];

        if ($meta['total'] === 0 && $count > 0) {
            $meta['total'] = $count;
        }

        return $meta;
    }

    /**
     * @return array<string, int>
     */
    private function defaultPaginationMeta(): array
    {
        return [
            'current_page' => 1,
            'last_page' => 1,
            'per_page' => 15,
            'total' => 0,
        ];
    }



          /**
             * Build a log payload for registration (no file contents, only file names and scalar fields)
             *
             * @param array $data
             * @param array $fields
             * @return array
             */
            protected function buildLogPayload(array $data, array $fields): array
            {
                $payload = $fields;
                // Add file names for uploads
                foreach (['photo', 'aadhar_document', 'pan_document', 'signature'] as $fileField) {
                    if (isset($data[$fileField]) && $data[$fileField] instanceof \Illuminate\Http\UploadedFile) {
                        $payload[$fileField] = $data[$fileField]->getClientOriginalName();
                    }
                }
                // Education documents
                if (!empty($data['education'])) {
                    foreach ($data['education'] as $i => $edu) {
                        if (isset($edu['document']) && $edu['document'] instanceof \Illuminate\Http\UploadedFile) {
                            $payload["education[$i][document]"] = $edu['document']->getClientOriginalName();
                        }
                    }
                }
                return $payload;
            }

}
