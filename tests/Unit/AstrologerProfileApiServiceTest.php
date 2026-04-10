<?php

use App\Services\Api\AstrologerApiService;
use Illuminate\Support\Facades\Http;

test('profile update sends signature using upstream field name', function () {
    $config = array_merge(config('auth_api'), [
        'base_url' => 'https://example.test/api/v1',
    ]);

    Http::fake([
        'https://example.test/api/v1/astrologer/profile' => Http::response([
            'success' => true,
            'data' => [
                'signature' => 'data:image/png;base64,new-signature',
            ],
        ], 200),
    ]);

    $service = new AstrologerApiService($config);

    $result = $service->updateAuthenticatedProfile([
        'first_name' => 'Raju',
        'signature' => 'data:image/png;base64,new-signature',
    ], 'token-123');

    Http::assertSent(function ($request) {
        $body = $request->body();

        return $request->url() === 'https://example.test/api/v1/astrologer/profile'
            && str_contains($body, 'name="signature"')
            && str_contains($body, 'data:image/png;base64,new-signature')
            && str_contains($body, 'name="astrologer_signature_image"');
    });

    expect($result['success'])->toBeTrue();
});
