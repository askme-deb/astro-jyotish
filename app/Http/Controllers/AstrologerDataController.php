<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AstrologerApiService;

class AstrologerDataController extends Controller
{
    protected $astrologerApiService;

    public function __construct(AstrologerApiService $astrologerApiService)
    {
        $this->astrologerApiService = $astrologerApiService;
    }

    public function getLanguages()
    {
        $languages = $this->astrologerApiService->getLanguages();
        return response()->json($languages);
    }

    public function getSkills()
    {
        $skills = $this->astrologerApiService->getSkills();
        return response()->json($skills);
    }
}
