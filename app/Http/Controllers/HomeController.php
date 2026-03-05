<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Api\AstrologerApiService;
use App\Services\Api\BlogService;

class HomeController extends Controller
{
    protected AstrologerApiService $astrologerApiService;
    protected BlogService $blogService;

    public function __construct(AstrologerApiService $astrologerApiService, BlogService $blogService)
    {
        $this->astrologerApiService = $astrologerApiService;
        $this->blogService = $blogService;
    }
    public function index(Request $request)
    {
        $forceRefresh = $request->query('refresh', false);
        $astrologers = $this->astrologerApiService->getAstrologers($forceRefresh);
        $blogs = $this->blogService->getBlogs($forceRefresh);
        return view('welcome', compact('astrologers', 'blogs'));
    }
}
