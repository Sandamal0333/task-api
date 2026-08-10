<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskService
{
    public function createTask(User $user, array $data): Task
    {
        return $user->tasks()->create($data);
    }

    public function getUserTasks(User $user, Request $request)
    {
        $query = $user->tasks();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sorting
        $allowedSorts = ['title', 'status', 'created_at', 'updated_at'];

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }

        if (!in_array(strtolower($direction), ['asc', 'desc'])) {
            $direction = 'desc';
        }

        $query->orderBy($sort, $direction);

        return $query->paginate(8);
    }

    public function getTask(Task $task): Task
    {
        return $task;
    }

    public function updateTask(Task $task, array $data): Task
    {
        $task->update($data);

        return $task->refresh();
    }

    public function deleteTask(Task $task): bool
    {
        return $task->delete();
    }
}
