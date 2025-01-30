<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{

    use RefreshDatabase;

    #[Test]
    public function displays_registration_page() {
        $response = $this->get('/register');
        $response->assertStatus(200)->assertViewIs('auth.register');
    }

    #[Test]
    public function it_registers_a_new_user_and_logs_them_in()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123' // Must match for validation
        ];

        // Submit the form
        $response = $this->post('/register', $userData);

        // Ensure the user is redirected to the dashboard
        $response->assertRedirect('/dashboard');

        // Ensure the user exists in the database
        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);

        // Ensure the user is logged in
        $this->assertAuthenticated();
    }

    #[Test]
    public function displays_login_page() {
        $response = $this->get('/login');
        $response->assertStatus(200)->assertViewIs('auth.login');
    }

    #[Test]
    public function it_logs_in_a_user_with_correct_credentials()
    {
        // Create a test user
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123'), // Hash password
        ]);

        // Attempt login with correct credentials
        $response = $this->post('/login', [
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        // Ensure user is authenticated
        $this->assertAuthenticatedAs($user);

        // Ensure user is redirected to the dashboard
        $response->assertRedirect(route('dashboard'));

        // Ensure success message exists in the session
        $response->assertSessionHas('success', 'Login successful.');
    }

    #[Test]
    public function it_fails_login_with_invalid_credentials()
    {
        // Create a user
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Attempt login with wrong password
        $response = $this->post('/login', [
            'email' => 'john@example.com',
            'password' => 'wrongpassword',
        ]);

        // Ensure user is not authenticated
        $this->assertGuest();

        // Ensure user is redirected back with an error message
        $response->assertRedirect();
        $response->assertSessionHasErrors(['email' => 'Invalid email or password.']);
    }

    #[Test]
    public function it_requires_email_and_password_to_login()
    {
        // Attempt login with missing fields
        $response = $this->post('/login', []);

        // Ensure validation errors are returned
        $response->assertSessionHasErrors(['email', 'password']);
    }
}
