<?php

namespace Tests\Feature\Auth;

use App\Events\UserRegistered;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_valid_data(): void
    {
        Event::fake();

        $userData = [
            'name' => 'João Silva',
            'cpf' => '12345678901',
            'email' => 'joao@example.com',
            'phone' => '11999999999',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'date' => '1990-01-01',
            'sexo' => 'M',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Usuário registrado com sucesso! Verifique seu e-mail para ativar sua conta.',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'joao@example.com',
            'name' => 'João Silva',
            'cpf' => '12345678901',
        ]);

        $user = User::where('email', 'joao@example.com')->first();
        $this->assertTrue(Hash::check('Password123!', $user->password));
        $this->assertNull($user->email_verified_at);

        Event::assertDispatched(UserRegistered::class);
    }

    public function test_user_cannot_register_with_invalid_email(): void
    {
        $userData = [
            'name' => 'João Silva',
            'cpf' => '12345678901',
            'email' => 'invalid-email',
            'phone' => '11999999999',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'date' => '1990-01-01',
            'sexo' => 'M',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_cannot_register_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'joao@example.com']);

        $userData = [
            'name' => 'João Silva',
            'cpf' => '12345678901',
            'email' => 'joao@example.com',
            'phone' => '11999999999',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'date' => '1990-01-01',
            'sexo' => 'M',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_cannot_register_with_weak_password(): void
    {
        $userData = [
            'name' => 'João Silva',
            'cpf' => '12345678901',
            'email' => 'joao@example.com',
            'phone' => '11999999999',
            'password' => '123456',
            'password_confirmation' => '123456',
            'date' => '1990-01-01',
            'sexo' => 'M',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }
}
