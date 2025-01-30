<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase; // Ensures a clean database for testing

    #[Test]
    public function it_redirects_guests_to_login_page()
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login'); // Ensure guests are redirected
    }

    #[Test]
    public function authenticated_users_can_view_dashboard()
    {
        // Create a user
        $user = User::factory()->create();

        // Act as the authenticated user
        $response = $this->actingAs($user)->get('/dashboard');

        // Assert that the page loads correctly
        $response->assertStatus(200)
            ->assertViewIs('dashboard')
            ->assertViewHas('tasks')
            ->assertViewHas('user', $user);
    }

    #[Test]
    public function it_filters_tasks_based_on_search_query()
    {
        $user = User::factory()->create();
        Task::factory()->create([
            'title' => 'Fix Bug',
            'status' => 'pending',
            'user_id' => $user->id,
        ]);
        Task::factory()->create([
            'title' => 'Write Tests',
            'status' => 'in_progress',
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get('/dashboard?search=Bug');

        $response->assertStatus(200)
            ->assertViewIs('dashboard')
            ->assertViewHas('tasks', function ($tasks) {
                return $tasks->count() == 1 && $tasks->first()->title === 'Fix Bug';
            });
    }

    #[Test]
    public function it_filters_tasks_by_status()
    {
        $user = User::factory()->create();

        // Creating tasks with different statuses
        Task::factory()->create([
            'title' => 'Fix Bug',
            'status' => 'pending',
            'user_id' => $user->id,
        ]);

        Task::factory()->create([
            'title' => 'Write Tests',
            'status' => 'in_progress',
            'user_id' => $user->id,
        ]);

        // ✅ Ensure at least one "completed" task exists
        $completedTask = Task::factory()->create([
            'title' => 'Deploy App',
            'status' => 'completed',
            'user_id' => $user->id,
        ]);

        // Act as an authenticated user and filter by "completed" status
        $response = $this->actingAs($user)->get('/dashboard?status=completed');

        // Ensure the response is correct
        $response->assertStatus(200)
            ->assertViewIs('dashboard')
            ->assertViewHas('tasks', function ($tasks) use ($completedTask) {
                return $tasks->count() == 1 && $tasks->first()->id === $completedTask->id;
            });
    }


    #[Test]
    public function it_paginates_tasks()
    {
        $user = User::factory()->create();
        Task::factory()->count(15)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200)
            ->assertViewIs('dashboard')
            ->assertViewHas('tasks', function ($tasks) {
                return $tasks->count() == 10; // Only 10 tasks should be shown per page
            });
    }
}
