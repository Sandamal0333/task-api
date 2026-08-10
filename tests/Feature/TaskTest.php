<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_task()
    {
        $user = User::factory()->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader(
            'Authorization',
            'Bearer ' . $token
        )->postJson('/api/tasks', [
            'title' => 'Complete Laravel API',
            'description' => 'Write automated tests',
            'status' => 'Pending',
        ]);

        $response->assertStatus(201);

        $response->assertJson([
            'message' => 'Task created successfully'
        ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Complete Laravel API',
            'user_id' => $user->id
        ]);
    }

    public function test_user_can_view_single_task()
    {
        $user = User::factory()->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $task = $user->tasks()->create([
            'title' => 'Learn Laravel',
            'description' => 'Complete API project',
            'status' => 'Pending', // Use one of your valid statuses
        ]);

        $response = $this->withHeader(
            'Authorization',
            'Bearer ' . $token
        )->getJson('/api/tasks/' . $task->id);

        $response->assertStatus(200);

        $response->assertJsonPath('data.id', $task->id);
        $response->assertJsonPath('data.title', 'Learn Laravel');
    }

    public function test_user_can_update_task()
    {
        $user = User::factory()->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $task = $user->tasks()->create([
            'title' => 'Old Title',
            'description' => 'Old Description',
            'status' => 'Pending',
        ]);

        $response = $this->withHeader(
            'Authorization',
            'Bearer ' . $token
        )->putJson('/api/tasks/' . $task->id, [
            'title' => 'New Title',
            'description' => 'New Description',
            'status' => 'Pending', // Use a valid status from your app
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'New Title',
            'description' => 'New Description',
            'status' => 'Pending',
        ]);
    }

    public function test_user_can_delete_task()
    {
        $user = User::factory()->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $task = $user->tasks()->create([
            'title' => 'Delete Me',
            'description' => 'Temporary task',
            'status' => 'Pending',
        ]);

        $response = $this->withHeader(
            'Authorization',
            'Bearer ' . $token
        )->deleteJson('/api/tasks/' . $task->id);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_user_cannot_view_other_users_task()
    {
        $userOne = User::factory()->create();
        $userTwo = User::factory()->create();

        $token = $userTwo->createToken('test-token')->plainTextToken;

        $task = $userOne->tasks()->create([
            'title' => 'Private Task',
            'description' => 'User one task',
            'status' => 'Pending',
        ]);

        $response = $this->withHeader(
            'Authorization',
            'Bearer ' . $token
        )->getJson('/api/tasks/' . $task->id);

        $response->assertStatus(403);
    }

    public function test_user_cannot_update_other_users_task()
    {
        $userOne = User::factory()->create();
        $userTwo = User::factory()->create();

        $token = $userTwo->createToken('test-token')->plainTextToken;

        $task = $userOne->tasks()->create([
            'title' => 'Original Task',
            'description' => 'User one task',
            'status' => 'Pending',
        ]);

        $response = $this->withHeader(
            'Authorization',
            'Bearer ' . $token
        )->putJson('/api/tasks/' . $task->id, [
            'title' => 'Hacked Task',
            'description' => 'Changed by another user',
            'status' => 'Completed',
        ]);

        $response->assertStatus(403);
    }

    public function test_user_cannot_delete_other_users_task()
    {
        $userOne = User::factory()->create();
        $userTwo = User::factory()->create();

        $token = $userTwo->createToken('test-token')->plainTextToken;

        $task = $userOne->tasks()->create([
            'title' => 'Private Task',
            'description' => 'User one task',
            'status' => 'Pending',
        ]);

        $response = $this->withHeader(
            'Authorization',
            'Bearer ' . $token
        )->deleteJson('/api/tasks/' . $task->id);

        $response->assertStatus(403);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Private Task',
        ]);
    }

    public function test_user_cannot_create_task_without_title()
    {
        $user = User::factory()->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader(
            'Authorization',
            'Bearer ' . $token
        )->postJson('/api/tasks', [
            'description' => 'Task without title',
            'status' => 'Pending',
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'title'
        ]);
    }

    public function test_user_cannot_create_task_with_invalid_status()
    {
        $user = User::factory()->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader(
            'Authorization',
            'Bearer ' . $token
        )->postJson('/api/tasks', [
            'title' => 'Learn Laravel',
            'description' => 'Testing invalid status',
            'status' => 'wrong_status',
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'status'
        ]);
    }

    public function test_user_gets_404_when_task_does_not_exist(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $response = $this->getJson('/api/tasks/99999');
        $response->assertStatus(404);
    }

    public function test_user_cannot_view_another_users_task(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $task = $otherUser->tasks()->create([
            'title' => 'Other User Task',
            'description' => 'This belongs to another user',
            'status' => 'Pending',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/tasks/' . $task->id);

        $response->assertStatus(403);
    }

    public function test_user_cannot_update_another_users_task(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $task = $otherUser->tasks()->create([
            'title' => 'Other User Task',
            'description' => 'This belongs to another user',
            'status' => 'Pending',
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/tasks/' . $task->id, [
            'title' => 'Trying to update',
            'description' => 'Should not work',
            'status' => 'Completed',
        ]);

        $response->assertStatus(403);
    }

    public function test_user_cannot_delete_another_users_task(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $task = $otherUser->tasks()->create([
            'title' => 'Other User Task',
            'description' => 'This belongs to another user',
            'status' => 'Pending',
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson('/api/tasks/' . $task->id);

        $response->assertStatus(403);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_user_is_rate_limited_after_too_many_requests(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        for ($i = 0; $i < 60; $i++) {
            $this->getJson('/api/tasks');
        }

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(429);
    }
}
