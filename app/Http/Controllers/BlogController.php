<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Api\BlogService;

class BlogController extends Controller
{
    protected BlogService $blogService;

    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }

    public function show($slug)
    {
        $blogs = $this->blogService->getBlogs();
        $blog = collect($blogs)->first(function ($b) use ($slug) {
            return ($b['slug'] ?? $b['id']) == $slug;
        });
        if (!$blog) {
            abort(404);
        }
        // Optionally, fetch full content from API if available
        return view('blog-details', compact('blog'));
    }
}
