<?php

namespace App\Services\Api;

use App\Services\Api\Clients\BaseApiClient;

class LocationApiService extends BaseApiClient
{
        public function __construct(array $config = null)
        {
            parent::__construct($config ?? config('auth_api'));
        }

    /**
     * Get state list for a country (default: 101 for India)
     * @param int $countryId
     * @return array
     */
    public function getStateList($countryId = 101): array
    {
        return $this->request('POST', 'get-state-list', [
            'json' => [
                'country_id' => $countryId,
            ],
        ]);
    }

    /**
     * Get city list for a state
     * @param int $stateId
     * @return array
     */
    public function getCityList($stateId): array
    {
        return $this->request('POST', 'get-city-list', [
            'json' => [
                'state_id' => $stateId,
            ],
        ]);
    }
}
