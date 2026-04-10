<?php

use App\Services\Api\AstrologerApiService as ApiAstrologerApiService;
use App\Services\AstrologerApiService;
use Illuminate\Support\Facades\Http;

test('languages are loaded from the canonical api list with original ids', function () {
    $config = array_merge(config('auth_api'), [
        'base_url' => 'https://example.test/api/v1',
    ]);

    Http::fake([
        'https://example.test/api/v1/languages' => Http::response([
            [
                'id' => 1,
                'name' => 'Hindi',
                'code' => 'hi',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'id' => 12,
                'name' => 'Odia',
                'code' => 'or',
                'is_active' => true,
                'sort_order' => 12,
            ],
            [
                'id' => 99,
                'name' => 'Retired',
                'code' => 'xx',
                'is_active' => false,
                'sort_order' => 99,
            ],
        ], 200),
    ]);

    $service = new AstrologerApiService(new ApiAstrologerApiService($config));

    expect($service->getLanguages())->toBe([
        [
            'id' => 1,
            'name' => 'Hindi',
            'code' => 'hi',
            'is_active' => true,
            'sort_order' => 1,
        ],
        [
            'id' => 12,
            'name' => 'Odia',
            'code' => 'or',
            'is_active' => true,
            'sort_order' => 12,
        ],
    ]);
});

test('skills are loaded from the canonical api list with original ids', function () {
    $config = array_merge(config('auth_api'), [
        'base_url' => 'https://example.test/api/v1',
    ]);

    Http::fake([
        'https://example.test/api/v1/skills' => Http::response([
            [
                'id' => 9,
                'name' => 'Career Counseling',
                'slug' => 'career-counseling',
                'status' => true,
            ],
            [
                'id' => 14,
                'name' => 'Muhurat',
                'slug' => 'muhurat',
                'status' => true,
            ],
            [
                'id' => 40,
                'name' => 'Hidden Skill',
                'slug' => 'hidden-skill',
                'status' => false,
            ],
        ], 200),
    ]);

    $service = new AstrologerApiService(new ApiAstrologerApiService($config));

    expect($service->getSkills())->toBe([
        [
            'id' => 9,
            'name' => 'Career Counseling',
            'slug' => 'career-counseling',
            'status' => true,
        ],
        [
            'id' => 14,
            'name' => 'Muhurat',
            'slug' => 'muhurat',
            'status' => true,
        ],
    ]);
});
