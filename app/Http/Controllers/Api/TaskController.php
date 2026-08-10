<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use OpenApi\Attributes as OA;
use App\Services\TaskService;

class TaskController extends Controller
{
    public function __construct(
        private TaskService $taskService
    ) {}

    #[OA\Get(
        path: "/api/tasks",
        summary: "Get all tasks",
        tags: ["Tasks"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "search",
                in: "query",
                required: false,
                description: "Search by title or description",
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "status",
                in: "query",
                required: false,
                description: "Filter by task status",
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "sort",
                in: "query",
                required: false,
                description: "Sort by column",
                schema: new OA\Schema(
                    type: "string",
                    enum: ["title", "status", "created_at", "updated_at"]
                )
            ),
            new OA\Parameter(
                name: "direction",
                in: "query",
                required: false,
                description: "Sort direction",
                schema: new OA\Schema(
                    type: "string",
                    enum: ["asc", "desc"]
                )
            ),
            new OA\Parameter(
                name: "page",
                in: "query",
                required: false,
                description: "Page number",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of tasks"
            ),
            new OA\Response(
                response: 401,
                description: "Unauthenticated"
            )
        ]
    )]
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tasks = $this->taskService->getUserTasks(
            auth()->user(),
            $request
        );

        return TaskResource::collection($tasks);
    }


    #[OA\Post(
        path: "/api/tasks",
        summary: "Create a new task",
        tags: ["Tasks"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["title", "status"],
                properties: [
                    new OA\Property(
                        property: "title",
                        type: "string",
                        example: "Complete Laravel project"
                    ),
                    new OA\Property(
                        property: "description",
                        type: "string",
                        example: "Finish Task API documentation"
                    ),
                    new OA\Property(
                        property: "status",
                        type: "string",
                        example: "Pending"
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Task created"),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $task = $this->taskService->createTask(
            auth()->user(),
            $request->validated()
        );

        return response()->json([
            'message' => 'Task created successfully',
            'task' => new TaskResource($task),
        ], 201);
    }


    #[OA\Get(
        path: "/api/tasks/{task}",
        summary: "Get a single task",
        tags: ["Tasks"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "task",
                in: "path",
                required: true,
                description: "Task ID",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Task details"),
            new OA\Response(response: 404, description: "Task not found")
        ]
    )]
    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);

        return new TaskResource($task);
    }


    #[OA\Put(
        path: "/api/tasks/{task}",
        summary: "Update a task",
        tags: ["Tasks"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "task",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: "title",
                        type: "string",
                        example: "Updated task title"
                    ),
                    new OA\Property(
                        property: "description",
                        type: "string",
                        example: "Updated description"
                    ),
                    new OA\Property(
                        property: "status",
                        type: "string",
                        example: "Completed"
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Task updated"),
            new OA\Response(response: 404, description: "Task not found")
        ]
    )]
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        // Update task
        $this->taskService->updateTask(
            $task,
            $request->validated()
        );

        return response()->json([
            'message' => 'Task updated successfully',
            'task' => new TaskResource($task),
        ]);
    }


    #[OA\Delete(
        path: "/api/tasks/{task}",
        summary: "Delete a task",
        tags: ["Tasks"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "task",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Task deleted"),
            new OA\Response(response: 404, description: "Task not found")
        ]
    )]
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        // Delete task
        $this->taskService->deleteTask($task);

        return response()->json([
            'message' => 'Task deleted successfully'
        ]);
    }
}
