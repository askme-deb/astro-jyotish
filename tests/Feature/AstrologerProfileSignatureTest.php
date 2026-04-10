<?php

use App\Services\AstrologerApiService;

it('prefers the saved signature image over a stale signature url on profile load', function () {
    $service = Mockery::mock(AstrologerApiService::class);
    $service->shouldReceive('getProfile')
        ->once()
        ->with('token-123')
        ->andReturn([
            'success' => true,
            'data' => [
                'first_name' => 'Raju',
                'last_name' => 'Sharma',
                'email' => 'raju@example.com',
                'mobile_no' => '9999999999',
                'signature_url' => 'https://example.test/old-signature.png',
                'astrologer_signature_image' => 'data:image/png;base64,new-signature',
            ],
        ]);
    $service->shouldReceive('getLanguages')->once()->andReturn([]);
    $service->shouldReceive('getSkills')->once()->andReturn([]);

    $this->app->instance(AstrologerApiService::class, $service);

    $response = $this->withSession([
        'api_user_id' => 42,
        'auth.api_token' => 'token-123',
        'auth.user' => [],
    ])->get(route('astrologer.profile.show'));

    $response->assertOk();
    $response->assertViewHas('profile', function (array $profile) {
        return ($profile['signature'] ?? null) === 'data:image/png;base64,new-signature';
    });
    $response->assertSee('data:image/png;base64,new-signature', false);
    $response->assertDontSee('https://example.test/old-signature.png', false);
});
