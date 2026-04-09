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

    /**
     * Get all unique languages from astrologers
     */
    public function getLanguages()
    {
        $astrologers = $this->apiService->getAstrologers() ?? [];
        $languages = [];
        foreach ($astrologers as $astrologer) {
            if (!empty($astrologer['languages'])) {
                $langs = is_array($astrologer['languages']) ? $astrologer['languages'] : explode(',', $astrologer['languages']);
                foreach ($langs as $lang) {
                    $lang = trim($lang);
                    if ($lang !== '' && !in_array($lang, $languages)) {
                        $languages[] = $lang;
                    }
                }
            }
        }
        // Return as array of objects with id and name for frontend
        return array_map(function($lang, $idx) {
            return ['id' => $idx + 1, 'name' => $lang];
        }, $languages, array_keys($languages));
    }

    /**
     * Get all unique skills from astrologers
     */
    public function getSkills()
    {
        $astrologers = $this->apiService->getAstrologers() ?? [];
        $skills = [];
        foreach ($astrologers as $astrologer) {
            if (!empty($astrologer['skills'])) {
                $skl = is_array($astrologer['skills']) ? $astrologer['skills'] : explode(',', $astrologer['skills']);
                foreach ($skl as $skill) {
                    $skill = trim($skill);
                    if ($skill !== '' && !in_array($skill, $skills)) {
                        $skills[] = $skill;
                    }
                }
            }
        }
        // Return as array of objects with id and name for frontend
        return array_map(function($skill, $idx) {
            return ['id' => $idx + 1, 'name' => $skill];
        }, $skills, array_keys($skills));
    }
}
