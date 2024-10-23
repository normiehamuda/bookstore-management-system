<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
  use RefreshDatabase;

  protected function setUp(): void
  {
    parent::setUp();
    $this->seed();
  }

  public function test_user_can_register()
  {
    $this->seed();
    $userData = [
      'name' => 'Test User',
      'email' => 'test@example.com',
      'password' => 'password',
      'role' => 2,
    ];

    $response = $this->postJson('/api/register', $userData);

    $response->assertStatus(201)
      ->assertJsonStructure([
        'token',
        'message'
      ]);

    $this->assertDatabaseHas('users', [
      'email' => 'test@example.com'
    ]);
  }

  public function test_user_can_login()
  {

    $user = User::factory()->create([
      'password' => bcrypt('password'),
      'role_id' => 2,
    ]);

    $credentials = [
      'email' => $user->email,
      'password' => 'password',
    ];

    $response = $this->postJson('/api/login', $credentials);

    $response->assertStatus(200)
      ->assertJsonStructure([
        'token',
        'message'
      ]);
  }

  public function test_user_cannot_login_with_incorrect_password()
  {

    $user = User::factory()->create([
      'password' => bcrypt('password'),
      'role_id' => 2,
    ]);

    $credentials = [
      'email' => $user->email,
      'password' => 'incorrect_password',
    ];

    $response = $this->postJson('/api/login', $credentials);

    $response->assertStatus(401)
      ->assertJson([
        'message' => 'Invalid credentials'
      ]);
  }


  public function test_authenticated_user_can_logout()
  {

    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeaders([
      'Authorization' => 'Bearer ' . $token,
    ])->get('/api/logout');

    $response->assertStatus(200)
      ->assertJson([
        'message' => 'User logged out successfully'
      ]);
  }

  public function test_registration_fails_with_invalid_email_format()
  {
    $userData = [
      'name' => 'Test User',
      'email' => 'invalid-email',
      'password' => 'password',
      'role' => 2,
    ];

    $response = $this->postJson('/api/register', $userData);

    $response->assertStatus(422)
      ->assertJsonValidationErrors(['email']);
  }

  public function test_registration_fails_with_duplicate_email()
  {
    $existingUser = User::factory()->create();

    $userData = [
      'name' => 'Test User 2',
      'email' => $existingUser->email,
      'password' => 'password',
      'role' => 2,
    ];

    $response = $this->postJson('/api/register', $userData);

    $response->assertStatus(422)
      ->assertJsonValidationErrors(['email']);
  }

  public function test_registration_fails_with_short_password()
  {
    $userData = [
      'name' => 'Test User',
      'email' => 'test@example.com',
      'password' => 'short',
      'role' => 2,
    ];

    $response = $this->postJson('/api/register', $userData);

    $response->assertStatus(422)
      ->assertJsonValidationErrors(['password']);
  }

  public function test_registration_fails_with_invalid_role()
  {
    $userData = [
      'name' => 'Test User',
      'email' => 'test@example.com',
      'password' => 'password',
      'role' => 999,
    ];

    $response = $this->postJson('/api/register', $userData);

    $response->assertStatus(422)
      ->assertJsonValidationErrors(['role']);
  }

  public function test_user_can_logout_and_token_is_deleted()
  {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeaders([
      'Authorization' => 'Bearer ' . $token,
    ])->get('/api/logout');

    $response->assertStatus(200)
      ->assertJson([
        'message' => 'User logged out successfully'
      ]);

    $this->assertDatabaseMissing('personal_access_tokens', [
      'tokenable_id' => $user->id,
      'tokenable_type' => get_class($user),
      'name' => 'test-token',
    ]);
  }

  public function test_unauthorized_access_after_logout()
  {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $this->withHeaders([
      'Authorization' => 'Bearer ' . $token,
    ])->get('/api/logout');

    $response = $this->postJson('/api/books', [
      'title' => 'Test Book',
      'author' => 'Test Author',
      'isbn' => '1234567890',
      'price' => 19.99

    ], [
      'Authorization' => 'Bearer ' . $token,
    ]);
    $response->assertStatus(401);
  }
}