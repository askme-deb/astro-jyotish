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
    * @return array<string, mixed>
     */
    public function createAstrologer(array $data): array
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

        if (!is_array($result)) {
            $result = [];
        }

        $success = $response->successful();

        if (array_key_exists('status', $result)) {
            $success = $success && (bool) $result['status'];
        }

        if (array_key_exists('success', $result)) {
            $success = $success && (bool) $result['success'];
        }

        if (!$success) {
            $errors = $this->extractValidationErrors($result);
            $message = $this->extractFirstErrorMessage($result, $errors, 'Registration failed. Please try again later.');
            $allErrors = $this->flattenValidationErrors($errors);

            if ($allErrors === [] && $message !== '') {
                $allErrors[] = $message;
            }

            Log::warning('[API] Astrologer registration failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'result' => $result
            ]);

            return [
                'success' => false,
                'message' => $message,
                'errors' => $errors,
                'all_errors' => $allErrors,
                'status_code' => $response->status(),
            ];
        }

        $payload = $result['data'] ?? $result['astrologer'] ?? $result;
        $message = $result['message'] ?? 'Registration successful!';

        if (!is_string($message) || trim($message) === '') {
            $message = 'Registration successful!';
        }

        return [
            'success' => true,
            'message' => $message,
            'data' => $payload,
        ];
    }

    public function getAuthenticatedProfile(?string $token = null): array
    {
        $request = \Illuminate\Support\Facades\Http::baseUrl($this->baseUrl)
            ->timeout($this->timeoutSeconds)
            ->retry($this->retryTimes, $this->retrySleepMilliseconds, function ($exception) {
                return $exception instanceof \Illuminate\Http\Client\ConnectionException;
            })
            ->acceptJson();

        if ($token !== null && $token !== '') {
            $request = $request->withToken($token);
        } elseif ($this->token !== null && $this->token !== '') {
            $request = $request->withToken($this->token);
        }

        $response = $request->get('astrologer/profile');
        $result = $response->json();

        if (!is_array($result)) {
            $result = [];
        }

        if (!$response->successful()) {
            $errors = $this->extractValidationErrors($result);

            return [
                'success' => false,
                'message' => $this->extractFirstErrorMessage($result, $errors, 'Failed to load astrologer profile.'),
                'errors' => $errors,
                'status_code' => $response->status(),
            ];
        }

        return [
            'success' => true,
            'message' => is_string($result['message'] ?? null) ? trim((string) $result['message']) : 'Profile loaded successfully.',
            'data' => $result['data'] ?? $result['profile'] ?? $result,
        ];
    }

    public function updateAuthenticatedProfile(array $payload, ?string $token = null, string $method = 'PATCH'): array
    {
        $http = \Illuminate\Support\Facades\Http::baseUrl($this->baseUrl)
            ->timeout($this->timeoutSeconds)
            ->retry($this->retryTimes, $this->retrySleepMilliseconds, function ($exception) {
                return $exception instanceof \Illuminate\Http\Client\ConnectionException;
            })
            ->acceptJson();

        if ($token !== null && $token !== '') {
            $http = $http->withToken($token);
        } elseif ($this->token !== null && $this->token !== '') {
            $http = $http->withToken($this->token);
        }

        foreach (['photo', 'aadhar_document', 'pan_document', 'signature'] as $fileField) {
            if (isset($payload[$fileField]) && $payload[$fileField] instanceof \Illuminate\Http\UploadedFile) {
                $http = $http->attach($fileField, fopen($payload[$fileField]->getRealPath(), 'r'), $payload[$fileField]->getClientOriginalName());
            }
        }

        if (!empty($payload['education'])) {
            foreach ($payload['education'] as $index => $education) {
                if (isset($education['document']) && $education['document'] instanceof \Illuminate\Http\UploadedFile) {
                    $http = $http->attach("education[$index][document]", fopen($education['document']->getRealPath(), 'r'), $education['document']->getClientOriginalName());
                }
            }
        }

        $fields = [];
        foreach ($payload as $key => $value) {
            if (in_array($key, ['photo', 'aadhar_document', 'pan_document', 'signature', 'education', 'availabilities', 'languages', 'skills'], true)) {
                continue;
            }

            if (is_bool($value)) {
                $fields[$key] = $value ? '1' : '0';
                continue;
            }

            $fields[$key] = $value ?? '';
        }

        if (!empty($payload['languages'])) {
            foreach ($payload['languages'] as $language) {
                $fields['languages[]'][] = $language;
            }
        }

        if (!empty($payload['skills'])) {
            foreach ($payload['skills'] as $skill) {
                $fields['skills[]'][] = $skill;
            }
        }

        if (!empty($payload['education'])) {
            foreach ($payload['education'] as $index => $education) {
                $fields["education[$index][degree]"] = $education['degree'] ?? '';
                $fields["education[$index][institution]"] = $education['institution'] ?? '';
                $fields["education[$index][year]"] = $education['year'] ?? '';
            }
        }

        if (!empty($payload['availabilities'])) {
            foreach ($payload['availabilities'] as $index => $availability) {
                $fields["availabilities[$index][day]"] = $availability['day'] ?? '';

                if (!empty($availability['slots'])) {
                    foreach ($availability['slots'] as $slotIndex => $slot) {
                        $fields["availabilities[$index][slots][$slotIndex][from]"] = $slot['from'] ?? '';
                        $fields["availabilities[$index][slots][$slotIndex][to]"] = $slot['to'] ?? '';
                    }
                }
            }
        }

        $method = strtoupper($method) === 'PUT' ? 'PUT' : 'PATCH';
        $http = $http->asMultipart();
        $response = $method === 'PUT'
            ? $http->put('astrologer/profile', $fields)
            : $http->patch('astrologer/profile', $fields);
        $result = $response->json();

        if (!is_array($result)) {
            $result = [];
        }

        if (!$response->successful()) {
            $errors = $this->extractValidationErrors($result);

            return [
                'success' => false,
                'message' => $this->extractFirstErrorMessage($result, $errors, 'Failed to update astrologer profile.'),
                'errors' => $errors,
                'status_code' => $response->status(),
            ];
        }

        return [
            'success' => true,
            'message' => is_string($result['message'] ?? null) ? trim((string) $result['message']) : 'Profile updated successfully.',
            'data' => $result['data'] ?? $result['profile'] ?? $payload,
        ];
    }

    private function extractValidationErrors(array $result): array
    {
        $errors = $result['errors'] ?? data_get($result, 'data.errors') ?? data_get($result, 'error.errors');

        if (!is_array($errors)) {
            return [];
        }

        foreach ($errors as $field => $fieldErrors) {
            if (is_array($fieldErrors)) {
                foreach ($fieldErrors as $index => $fieldError) {
                    if (is_string($fieldError)) {
                        $errors[$field][$index] = $this->humanizeValidationMessage($fieldError);
                    }
                }

                continue;
            }

            if (is_string($fieldErrors)) {
                $errors[$field] = $this->humanizeValidationMessage($fieldErrors);
            }
        }

        return $errors;
    }

    private function extractFirstErrorMessage(array $result, array $errors, string $default): string
    {
        foreach ($errors as $fieldErrors) {
            if (is_array($fieldErrors)) {
                foreach ($fieldErrors as $fieldError) {
                    if (is_string($fieldError) && trim($fieldError) !== '') {
                        return $this->humanizeValidationMessage(trim($fieldError));
                    }
                }

                continue;
            }

            if (is_string($fieldErrors) && trim($fieldErrors) !== '') {
                return $this->humanizeValidationMessage(trim($fieldErrors));
            }
        }

        $message = $result['message'] ?? data_get($result, 'error.message') ?? data_get($result, 'data.message');

        if (is_string($message)) {
            $normalizedMessage = $this->extractMessageFromWrappedPayload($message);

            if ($normalizedMessage !== null) {
                return $this->humanizeValidationMessage($normalizedMessage);
            }
        }

        return is_string($message) && trim($message) !== ''
            ? $this->humanizeValidationMessage(trim($message))
            : $default;
    }

    private function flattenValidationErrors(array $errors): array
    {
        $allErrors = [];

        foreach ($errors as $fieldErrors) {
            if (is_array($fieldErrors)) {
                foreach ($fieldErrors as $fieldError) {
                    if (is_string($fieldError) && trim($fieldError) !== '') {
                        $allErrors[] = $this->humanizeValidationMessage(trim($fieldError));
                    }
                }

                continue;
            }

            if (is_string($fieldErrors) && trim($fieldErrors) !== '') {
                $allErrors[] = $this->humanizeValidationMessage(trim($fieldErrors));
            }
        }

        return array_values(array_unique($allErrors));
    }

    private function extractMessageFromWrappedPayload(string $message): ?string
    {
        $message = trim($message);

        if ($message === '') {
            return null;
        }

        $jsonStart = strpos($message, '{');
        if ($jsonStart === false) {
            return $this->extractMessageFromTruncatedPayload($message);
        }

        $decoded = json_decode(substr($message, $jsonStart), true);
        if (!is_array($decoded)) {
            return $this->extractMessageFromTruncatedPayload($message);
        }

        $errors = $this->extractValidationErrors($decoded);
        foreach ($errors as $fieldErrors) {
            if (is_array($fieldErrors) && isset($fieldErrors[0]) && is_string($fieldErrors[0]) && trim($fieldErrors[0]) !== '') {
                return $this->humanizeValidationMessage(trim($fieldErrors[0]));
            }

            if (is_string($fieldErrors) && trim($fieldErrors) !== '') {
                return $this->humanizeValidationMessage(trim($fieldErrors));
            }
        }

        $decodedMessage = $decoded['message'] ?? data_get($decoded, 'error.message') ?? data_get($decoded, 'data.message');

        return is_string($decodedMessage) && trim($decodedMessage) !== ''
            ? $this->humanizeValidationMessage(trim($decodedMessage))
            : null;
    }

    private function extractMessageFromTruncatedPayload(string $message): ?string
    {
        $messageKeyPosition = strpos($message, '"message"');
        if ($messageKeyPosition === false) {
            return null;
        }

        $valueStart = strpos($message, ':', $messageKeyPosition);
        if ($valueStart === false) {
            return null;
        }

        $openingQuote = strpos($message, '"', $valueStart);
        if ($openingQuote === false) {
            return null;
        }

        $openingQuote++;
        $length = strlen($message);
        $buffer = '';

        for ($index = $openingQuote; $index < $length; $index++) {
            $character = $message[$index];

            if ($character === '"' && $message[$index - 1] !== '\\') {
                break;
            }

            $buffer .= $character;
        }

        $decoded = stripcslashes($buffer);

        return trim($decoded) !== '' ? $this->humanizeValidationMessage(trim($decoded)) : null;
    }

    private function humanizeValidationMessage(string $message): string
    {
        return str_ireplace('1024 kilobytes', '1MB', $message);
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
