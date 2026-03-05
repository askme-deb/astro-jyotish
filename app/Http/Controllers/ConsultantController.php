<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Api\AstrologerApiService;

class ConsultantController extends Controller
{

    protected AstrologerApiService $astrologerApiService;

    public function __construct(AstrologerApiService $astrologerApiService)
    {
        $this->astrologerApiService = $astrologerApiService;
    }

    /**
     * Display a listing of astrologers from the API.
     */
    public function show(Request $request)
    {
        $forceRefresh = $request->query('refresh', false);
        $astrologers = $this->astrologerApiService->getAstrologers($forceRefresh);
        return view('consultant', compact('astrologers'));
    }

    public function profile($identifier)
    {
        
        return view('consultant-profile');
    }
}
