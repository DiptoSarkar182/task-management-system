<?php

namespace Tests\Feature;

use App\Jobs\SendTaskReminderJob;
use App\Mail\TaskCreatedMail;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function it_redirects_guests_to_login_page()
    {
        $response = $this->get('/tasks/*');

        $response->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_users_can_view_task_create_form()
    {
        $user = User::factory()->create();

        // Act as the authenticated user
        $response = $this->actingAs($user)->get('/tasks/create');

        // Assert the page loads successfully and returns the correct view
        $response->assertStatus(200)
            ->assertViewIs('tasks.create');
    }

    /** @test */
    public function authenticated_users_can_create_a_task()
    {
        // Fake storage, queue, and mail
        Storage::fake('public');
        Queue::fake();
        Mail::fake();

        // Create a user
        $user = User::factory()->create();

        // Simulate file upload
        $file = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        // Mock valid task data
        $taskData = [
            'title' => 'New Task',
            'description' => 'This is a sample task.',
            'due_date' => Carbon::now()->addDays(2)->toDateString(),
            'status' => 'pending',
            'attachment' => $file,
        ];

        // Act as the user and send POST request
        $response = $this->actingAs($user)->post('/tasks', $taskData);

        // Assert redirection to dashboard
        $response->assertRedirect(route('dashboard'))
            ->assertSessionHas('success', 'Task created & reminder scheduled!');

        // Check if task exists in database
        $this->assertDatabaseHas('tasks', [
            'title' => 'New Task',
            'description' => 'This is a sample task.',
            'status' => 'pending',
            'user_id' => $user->id,
        ]);

        // Check if file was stored
        Storage::disk('public')->assertExists('attachments/' . $file->hashName());

        // Assert the reminder job was dispatched
        Queue::assertPushed(SendTaskReminderJob::class);

        // Assert emails were queued
        Mail::assertQueued(TaskCreatedMail::class);
    }

    /** @test */
    public function unauthenticated_users_cannot_create_a_task()
    {
        // Mock task data
        $taskData = [
            'title' => 'Unauthorized Task',
            'description' => 'Trying to create a task without logging in.',
            'due_date' => Carbon::now()->addDays(2)->toDateString(),
            'status' => 'pending',
        ];

        // Attempt to create a task without authentication
        $response = $this->post('/tasks', $taskData);

        // Assert redirection to login
        $response->assertRedirect('/login');

        // Ensure the task was NOT created in the database
        $this->assertDatabaseMissing('tasks', ['title' => 'Unauthorized Task']);
    }

    /** @test */
    public function it_requires_all_fields_to_be_valid()
    {
        $user = User::factory()->create();

        // Attempt to create a task with missing fields
        $response = $this->actingAs($user)->post('/tasks', []);

        // Assert validation errors
        $response->assertSessionHasErrors(['title', 'due_date', 'status']);
    }

    /** @test */
    public function authenticated_users_can_view_task_edit_form()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a task assigned to the user
        $task = Task::factory()->create(['user_id' => $user->id]);

        // Act as the authenticated user and request the edit form
        $response = $this->actingAs($user)->get("/tasks/{$task->id}/edit");

        // Assert the page loads successfully and returns the correct view
        $response->assertStatus(200)
            ->assertViewIs('tasks.edit')
            ->assertViewHas('task', $task);
    }

    /** @test */
    public function unauthenticated_users_cannot_access_task_edit_form()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a task assigned to the user
        $task = Task::factory()->create(['user_id' => $user->id]);

        // Try to access the edit page without authentication
        $response = $this->get("/tasks/{$task->id}/edit");

        // Ensure the user is redirected to the login page
        $response->assertRedirect('/login');
    }

    /** @test */
    public function users_cannot_edit_tasks_they_do_not_own()
    {
        // Create two users
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        // Create a task assigned to the first user
        $task = Task::factory()->create(['user_id' => $owner->id]);

        // Act as a different user and attempt to access the edit form
        $response = $this->actingAs($otherUser)->get("/tasks/{$task->id}/edit");

        // Assert that the user is forbidden (403)
        $response->assertStatus(403);
    }

    /** @test */
    public function authorized_users_can_update_a_task()
    {
        Storage::fake('public'); // Fake storage

        // Create a user and a task assigned to them
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Old Task Title',
            'status' => 'pending',
        ]);

        // New data to update
        $updatedData = [
            'title' => 'Updated Task Title',
            'description' => 'Updated task description.',
            'due_date' => now()->addDays(3)->toDateString(),
            'status' => 'completed',
        ];

        // Act as the authenticated user and send a PUT request
        $response = $this->actingAs($user)->put("/tasks/{$task->id}", $updatedData);

        // Assert redirection to dashboard
        $response->assertRedirect(route('dashboard'))
            ->assertSessionHas('success', 'Task updated successfully!');

        // Check if the task is updated in the database
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task Title',
            'status' => 'completed',
        ]);
    }

    /** @test */
    public function users_cannot_update_tasks_they_do_not_own()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $owner->id,
            'title' => 'Original Task Title',
            'due_date' => now()->addDays(3)->toDateString(),
            'status' => 'pending',
        ]);

        // ✅ Catch Authorization Exception
        $this->withoutExceptionHandling();
        $this->expectException(HttpException::class);

        // Act as a different user
        $response = $this->actingAs($otherUser)->put("/tasks/{$task->id}", [
            'title' => 'Unauthorized Update',
            'due_date' => now()->addDays(5)->toDateString(),
            'status' => 'in_progress',
        ]);

        // ✅ Ensure Laravel throws a 403 Forbidden error
        $response->assertStatus(403);

        // ✅ Ensure the task is NOT updated
        $this->assertDatabaseMissing('tasks', ['title' => 'Unauthorized Update']);
    }

    /** @test */
    public function an_admin_can_update_any_task()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Original Task Title',
            'due_date' => now()->addDays(5)->toDateString(),
            'status' => 'pending', // ✅ Ensure the task has an initial status
        ]);

        // Admin updates the task
        $response = $this->actingAs($admin)->put("/tasks/{$task->id}", [
            'title' => 'Admin Updated Task',
            'due_date' => now()->addDays(5)->toDateString(),
            'status' => 'completed', // ✅ Include required status field
        ]);

        // Ensure admin can update the task
        $response->assertRedirect(route('dashboard'))
            ->assertSessionHas('success', 'Task updated successfully!');

        // Check the update is in the database
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Admin Updated Task',
            'status' => 'completed',
        ]);
    }

    /** @test */
    public function authorized_users_can_delete_their_own_task()
    {
        Storage::fake('public'); // Fake storage for testing file deletion

        // Create a user
        $user = User::factory()->create();

        // Create a task assigned to this user
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'attachment_path' => 'attachments/sample.pdf',
        ]);

        // Ensure the file exists in storage
        Storage::disk('public')->put($task->attachment_path, 'dummy content');

        // Act as the user and send DELETE request
        $response = $this->actingAs($user)->delete("/tasks/{$task->id}");

        // ✅ Ensure redirection and success message
        $response->assertRedirect(route('dashboard'))
            ->assertSessionHas('success', 'Task deleted successfully!');

        // ✅ Ensure task is deleted from the database
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);

        // ✅ Ensure attachment file is deleted
        Storage::disk('public')->assertMissing($task->attachment_path);
    }

    /** @test */
    public function an_admin_can_delete_any_task()
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'attachment_path' => 'attachments/sample.pdf',
        ]);

        Storage::disk('public')->put($task->attachment_path, 'dummy content');

        $response = $this->actingAs($admin)->delete("/tasks/{$task->id}");

        $response->assertRedirect(route('dashboard'))
            ->assertSessionHas('success', 'Task deleted successfully!');

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
        Storage::disk('public')->assertMissing($task->attachment_path);
    }

    /** @test */
    public function unauthorized_users_cannot_delete_tasks_they_do_not_own()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $owner->id,
        ]);

        // Act as a different user
        $response = $this->actingAs($otherUser)->delete("/tasks/{$task->id}");

        // ✅ Ensure forbidden access (403)
        $response->assertStatus(403);

        // ✅ Ensure task is NOT deleted
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function unauthenticated_users_cannot_delete_tasks()
    {
        // ✅ Ensure a user exists and assign it to the task
        $user = User::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $user->id, // ✅ Assign a user to avoid SQL error
        ]);

        // Attempt to delete without authentication
        $response = $this->delete("/tasks/{$task->id}");

        // ✅ Ensure unauthenticated users are redirected to login
        $response->assertRedirect('/login');

        // ✅ Ensure the task is still in the database
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function authenticated_users_can_download_an_existing_attachment()
    {
        // ✅ Use real storage instead of Storage::fake()
        Storage::disk('public')->put('attachments/sample.pdf', 'dummy content');

        // Create a user
        $user = User::factory()->create();

        // Create a task with an attachment
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'attachment_path' => 'attachments/sample.pdf',
        ]);

        // ✅ Ensure the file exists in storage
        Storage::disk('public')->assertExists($task->attachment_path);

        // Act as authenticated user and try downloading the file
        $response = $this->actingAs($user)->get("/tasks/{$task->id}/download");

        // ✅ Ensure the response is a file download
        $response->assertStatus(200);

        // ✅ Extract the actual response from TestResponse and check instance
        $this->assertInstanceOf(BinaryFileResponse::class, $response->baseResponse);

        // ✅ Clean up by deleting the test file
        Storage::disk('public')->delete($task->attachment_path);
    }

}
