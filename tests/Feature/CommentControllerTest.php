<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
    #[Test]
    public function authenticated_users_can_add_a_comment_to_a_task()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a task assigned to this user
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        // Act as authenticated user and post a comment
        $response = $this->actingAs($user)->post("/tasks/{$task->id}/comments", [
            'content' => 'This is a test comment.',
        ]);

        // ✅ Ensure redirection and success message
        $response->assertRedirect(route('tasks.show', $task->id))
            ->assertSessionHas('success', 'Comment added successfully!');

        // ✅ Ensure comment is stored in the database
        $this->assertDatabaseHas('comments', [
            'content' => 'This is a test comment.',
            'user_id' => $user->id,
            'commentable_id' => $task->id, // Ensure it's linked to the task
            'commentable_type' => 'App\Models\Task', // Polymorphic relationship
        ]);
    }
}
