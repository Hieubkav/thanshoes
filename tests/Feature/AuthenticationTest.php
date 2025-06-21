<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_can_be_rendered()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_register_page_can_be_rendered()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_email()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'login_field' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/');
    }

    public function test_users_can_authenticate_using_phone()
    {
        $user = User::factory()->create([
            'phone' => '84901234567',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'login_field' => '84901234567',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/');
    }

    public function test_users_can_not_authenticate_with_invalid_password()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'login_field' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_register_with_email()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/');
    }

    public function test_users_can_register_with_phone()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'phone' => '+84901234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/');
    }

    public function test_users_can_register_with_both_email_and_phone()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '(+84) 901-234-567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/');
    }

    public function test_users_can_logout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_authenticated_users_cannot_access_login_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/login');
        $response->assertRedirect('/');
    }

    public function test_authenticated_users_cannot_access_register_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/register');
        $response->assertRedirect('/');
    }

    public function test_guest_users_cannot_access_admin_routes()
    {
        $response = $this->get('/excel');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_users_can_access_admin_routes()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/excel');
        $response->assertStatus(200);
    }

    public function test_navbar_shows_correct_content_for_authenticated_user()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);
        $this->actingAs($user);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Test User');
        $response->assertSee('test@example.com');
        $response->assertSee('Đăng xuất');
    }

    public function test_navbar_shows_correct_content_for_guest()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Đăng nhập');
        $response->assertSee('Đăng ký');
        $response->assertDontSee('Đăng xuất');
    }
}
