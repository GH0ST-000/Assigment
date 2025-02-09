<?php

namespace Tests\Feature\Api;

use App\Models\User;
use DateTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AndroidTvTest extends TestCase
{
    use RefreshDatabase;

    protected $oneTimeCode;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        $client = \Laravel\Passport\Client::forceCreate([
            'user_id' => null,
            'name' => 'Test Personal Access Client',
            'secret' => Str::random(40),
            'redirect' => '',
            'personal_access_client' => true,
            'password_client' => false,
            'revoked' => false,
        ]);

        DB::table('oauth_personal_access_clients')->insert([
            'client_id' => $client->id,
            'created_at' => new DateTime,
            'updated_at' => new DateTime,
        ]);
    }

    public function test_android_tv_code_complete_flow()
    {
        $response = $this->postJson(route('generate-tv-code'));
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'success',
                'data' => [
                    'one_time_code',
                    'expires_at',
                ],
            ]);

        $oneTimeCode = $response['data']['one_time_code'];

        $response = $this->postJson(route('poll-tv-code'), ['code' => $oneTimeCode]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'success',
                'data' => [
                    'message',
                ],
            ])
            ->assertJson([
                'data' => [
                    'message' => 'Code valid, waiting for activation',
                ],
            ]);

        $response = $this->postJson(route('active-tv-code'), ['code' => $oneTimeCode]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'success',
                'data' => [
                    'access_token',
                ],
            ]);

    }

    public function test_activate_nonexistent_code()
    {
        $response = $this->postJson(route('active-tv-code'), ['code' => 'nonexistentcode']);

        $response->assertStatus(200)
            ->assertExactJson([
                'status' => 500,
                'success' => false,
                'error' => [
                    'code' => 400,
                    'message' => 'Invalid or expired code',
                ],
            ]);
    }

    public function test_poll_nonexistent_code()
    {
        $response = $this->postJson(route('poll-tv-code'), ['code' => 'nonexistentcode']);

        $response->assertStatus(200)
            ->assertExactJson([
                'status' => 500,
                'success' => false,
                'error' => [
                    'code' => 400,
                    'message' => 'Code invalid or expired',
                ],
            ]);
    }

    public function test_poll_and_activate_expired_code()
    {
        $response = $this->postJson(route('generate-tv-code'));
        $oneTimeCode = $response['data']['one_time_code'];

        sleep(300);

        $response = $this->postJson(route('poll-tv-code'), ['code' => $oneTimeCode]);
        $response->assertStatus(200)
            ->assertExactJson([
                'status' => 200,
                'success' => true,
                'data' => [
                    'message' => 'Code valid, waiting for activation',
                ],
            ]);
    }
}
