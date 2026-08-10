<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskServiceTest extends TestCase
{
    use RefreshDatabase;

    private TaskService $taskService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->taskService = new TaskService();
    }

    public function test_it_can_create_a_task(): void
    {
        $user = User::factory()->create();

        $task = $this->taskService->createTask($user, [
            'title' => 'Learn Laravel',
            'description' => 'Build a Task API',
            'status' => 'Pending',
        ]);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('Learn Laravel', $task->title);
        $this->assertEquals('Pending', $task->status);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Learn Laravel',
        ]);
    }

    public function test_it_can_get_a_task(): void
    {
        $user = User::factory()->create();

        $task = $user->tasks()->create([
            'title' => 'Learn Laravel',
            'description' => 'Build a Task API',
            'status' => 'Pending',
        ]);

        $result = $this->taskService->getTask($task);

        $this->assertInstanceOf(Task::class, $result);
        $this->assertEquals($task->id, $result->id);
        $this->assertEquals('Learn Laravel', $result->title);
    }

    public function test_it_can_update_a_task(): void
    {
        $user = User::factory()->create();

        $task = $user->tasks()->create([
            'title' => 'Learn Laravel',
            'description' => 'Build a Task API',
            'status' => 'Pending',
        ]);

        $result = $this->taskService->updateTask($task, [
            'title' => 'Learn Laravel Advanced',
            'description' => 'Complete the Task API',
            'status' => 'Completed',
        ]);

        $this->assertInstanceOf(Task::class, $result);
        $this->assertEquals('Learn Laravel Advanced', $result->title);
        $this->assertEquals('Complete the Task API', $result->description);
        $this->assertEquals('Completed', $result->status);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Learn Laravel Advanced',
            'status' => 'Completed',
        ]);
    }

    public function test_it_can_delete_a_task(): void
    {
        $user = User::factory()->create();

        $task = $user->tasks()->create([
            'title' => 'Learn Laravel',
            'description' => 'Build a Task API',
            'status' => 'Pending',
        ]);

        $result = $this->taskService->deleteTask($task);

        $this->assertTrue($result);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }
}
