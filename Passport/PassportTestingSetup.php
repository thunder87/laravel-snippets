<?php

namespace Tests\Feature;

use Tests\TestCase;
use Laravel\Passport\ClientRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup for testing Laravel passport.
     * Remember to modify the config to match your own setup.
     *
     * By using this, you are expected to inject your client id/secret
     * through a "proxy" middleware - see "PassportClientSecretProxy".
     */
    public function setUp()
    {
        parent::setUp();

        $clientRepository = new ClientRepository();

        $clientRepository->createPersonalAccessClient(
            null, 'Personal Access Client', 'http://localhost'
        );

        $client = $clientRepository->createPasswordGrantClient(
            null, 'Password Grant Client', 'http://localhost'
        );

        config([
            'auth.passport.password_client_id' => $client->id,
            'auth.passport.password_client_secret' => $client->secret,
        ]);
    }

    /** @test */
    public function test_that_a_user_can_login_and_receive_a_token_response()
    {
        $this
            ->json('post', 'oauth/token', [
                'username' => 'test@laravel.com',
                'password' => 'secret',
                'grant_type' => 'password',
            ])
            ->assertStatus(200)
            ->assertJsonStructure(['token_type', 'expires_in', 'access_token', 'refresh_token']);
    }
}
