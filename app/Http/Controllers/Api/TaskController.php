<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = auth()->user()->tasks()->latest()->get();

        return response()->json([
            'tasks' => $tasks
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $task = auth()->user()->tasks()->create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Task created successfully',
            'task' => $task,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Task not found'
            ], 404);
        }

        return response()->json([
            'task' => $task
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
