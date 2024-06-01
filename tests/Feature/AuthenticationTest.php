<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get(route('admin.login.form'));

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'email' => 'abc@gmail.com',
            'password' => bcrypt('a12345678X'),
        ]);

        $response = $this->post(route('admin.api.login.handle'), [
            'email' => $user->email,
            'password' => 'a12345678X',
        ]);

        $this->assertAuthenticated();
        $response->assertStatus(200);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post(route('admin.api.login.handle'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }
    public function test_users_not_enter_email(): void
    {
        $response = $this->post(route('admin.api.login.handle'), [
            'password' => 'a12345678X',
        ]);

        $response->assertSessionHasErrors('email');
    }
    public function test_users_not_enter_password(): void
    {
        $response = $this->post(route('admin.api.login.handle'), [
            'email' => 'abc@gmail.com',
        ]);
        $response->assertSessionHasErrors('password');
    }
}
