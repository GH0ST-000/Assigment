<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\PersonalAccessTokenResult;
use Tests\TestCase;

class UserRegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('passport:client', ['--personal' => 'default', '--name' => 'TestPersonalAccessClient']);

    }

    public function test_invoke_creates_user_and_returns_success_with_user_token()
    {
        $userDetails = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password1234',
            'password_confirmation' => 'password1234',
        ];

        $userMock = \Mockery::mock(User::class.'[createToken, create]');

        $tokenMock = \Mockery::mock(PersonalAccessTokenResult::class);
        $tokenMock->accessToken = 'mock_token';

        $userMock->shouldReceive('createToken')->andReturn($tokenMock);

        $userMock->shouldReceive('create')->andReturnUsing(function () use ($userDetails) {
            return new User([
                'name' => $userDetails['name'],
                'email' => $userDetails['email'],
            ]);
        });

        $this->app->instance(User::class, $userMock);

        $response = $this->postJson('/api/register', $userDetails);
        $response->assertStatus(200);
        $data = $response->json();
        $this->assertSame($userDetails['name'], $data['data']['user']['name']);
        $this->assertSame($userDetails['email'], $data['data']['user']['email']);
        $this->assertNotEmpty($data['data']['token']);
    }

    public function test_invoke_returns_validation_error_on_invalid_user_data()
    {
        $invalidUserDetails = [
            'password' => 'TestPassword',
        ];

        $response = $this->postJson('/api/register', $invalidUserDetails);

        $this->assertEquals(422, $response->getStatusCode());

        $data = $response->json();

        $this->assertArrayHasKey('email', $data['errors']);
    }
}
