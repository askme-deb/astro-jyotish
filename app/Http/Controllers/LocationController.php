<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Api\LocationApiService;

class LocationController extends Controller
{
    protected $locationApiService;

    public function __construct(LocationApiService $locationApiService)
    {
        $this->locationApiService = $locationApiService;
    }

    public function getStates(Request $request)
    {
        $countryId = $request->input('country_id', 101);
        $result = $this->locationApiService->getStateList($countryId);
        return response()->json($result);
    }

    public function getCities(Request $request)
    {
        $stateId = $request->input('state_id');
        if (!$stateId) {
            return response()->json(['error' => 'state_id is required'], 422);
        }
        $result = $this->locationApiService->getCityList($stateId);
        return response()->json($result);
    }
}
