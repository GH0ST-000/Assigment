<?php

namespace Tests\Feature\Api;

use App\Http\Controllers\Auth\LoginUserController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    private LoginUserController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = $this->app->make(LoginUserController::class);
    }

    public function test_invoke_returns_success_on_valid_credentials()
    {
        $this->withoutExceptionHandling();
        $mockUser = \Mockery::mock('App\Models\User[createToken]');
        $mockUser->shouldReceive('createToken')->andReturn((object) ['accessToken' => 'token_string']);
        $mockUser->email = 'test@example.com';

        Auth::shouldReceive('attempt')->andReturn(true);
        Auth::shouldReceive('user')->andReturn($mockUser);

        $response = $this->post('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $data = $response->json();

        $this->assertEquals('test@example.com', $data['data']['user']['email']);
        $this->assertEquals('token_string', $data['data']['token']);
    }

    public function test_invoke_returns_error_on_invalid_credentials()
    {
        Auth::shouldReceive('attempt')->andReturn(false);
        $response = $this->post('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $data = $response->json();

        $this->assertEquals('Invalid credentials', $data['error']['message']);
    }
}
