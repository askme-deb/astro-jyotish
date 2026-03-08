<?php

namespace App\Services\Api\Clients;

class UserApiService extends BaseApiClient
{
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * Update user profile via external API
     * @param array $data
     * @param string $token
     * @return array
     */
    public function updateProfile(array $data, string $token): array
    {
        $options = [
            'json' => $data,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ];
        return $this->request('PUT', '/user/profile', $options);
    }
}
