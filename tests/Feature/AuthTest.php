<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(201);

        $response->assertJson([
            'message' => 'User created successfully'
        ]);
    }

    public function test_user_can_login()
    {
        $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'token'
        ]);
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader(
            'Authorization',
            'Bearer ' . $token
        )->postJson('/api/logout');

        $response->assertStatus(200);

        $response->assertJson([
            'message' => 'Logged out successfully'
        ]);
    }

    public function test_user_cannot_login_with_invalid_password()
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);

        $response->assertJson([
            'message' => 'Invalid email or password',
        ]);
    }

    public function test_user_cannot_login_with_nonexistent_email()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'doesnotexist@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401);

        $response->assertJson([
            'message' => 'Invalid email or password',
        ]);
    }

    public function test_user_cannot_register_with_existing_email()
    {
        User::factory()->create([
            'email' => 'john@example.com',
        ]);

        $response = $this->postJson('/api/register', [
            'name' => 'Another John',
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'email',
        ]);
    }

    public function test_user_cannot_logout_without_authentication()
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401);

        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }

    public function test_logout_invalidates_token()
{
    $user = User::factory()->create();

    $token = $user->createToken('test-token');

    $plainTextToken = $token->plainTextToken;

    $this->withHeader(
        'Authorization',
        'Bearer ' . $plainTextToken
    )->postJson('/api/logout')->assertStatus(200);

    $this->assertDatabaseMissing('personal_access_tokens', [
        'id' => $token->accessToken->id,
    ]);
}
}
