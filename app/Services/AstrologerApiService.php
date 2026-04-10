<?php

namespace App\Services;

use App\Services\Api\AstrologerApiService as ApiAstrologerApiService;

class AstrologerApiService
{
    /**
     * Register a new astrologer via the external API.
     *
     * @param array $data
     * @return array|null
     */
    public function createAstrologer(array $data)
    {
        return $this->apiService->createAstrologer($data);
    }

    protected ApiAstrologerApiService $apiService;

    public function __construct(ApiAstrologerApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function getProfile(?string $token = null): array
    {
        return $this->apiService->getAuthenticatedProfile($token);
    }

    public function updateProfile(array $payload, ?string $token = null, string $method = 'PATCH'): array
    {
        return $this->apiService->updateAuthenticatedProfile($payload, $token, $method);
    }

    public function getLanguages(): array
    {
        return $this->apiService->getLanguages();
    }

    public function getSkills(): array
    {
        return $this->apiService->getSkills();
    }
}
