<?php

namespace App\Http\Controllers\API;

use App\Models\Task;
use App\Services\TaskService;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\IndexTaskRequest;
use App\Http\Resources\TaskResource;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\TaskRepositoryInterface;


class TaskController extends Controller
{
    public function __construct(
        private TaskRepositoryInterface $taskRepo,
        protected TaskService $taskService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(IndexTaskRequest $request)
    {
        $this->authorize('viewAny', Task::class);

        $date = $request->date ? now()->parse($request->date) : now();

        $tasks = $this->taskRepo->getUserTasksByDate(
            $request->user(),
            $date,
            $request->q
        );

        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $task = $this->taskService->create(
            $request->user(),
            $request->validated()
        );

        return new TaskResource($task);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);
        $task = $this->taskService->update($task, $request->validated());

        return new TaskResource($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $this->taskService->delete($task);

        return response()->json(['message' => 'Deleted']);
    }

    public function toggle(Task $task)
    {
        $this->authorize('toggle', $task);

        $task = $this->taskService->toggle($task);

        return new TaskResource($task);
    }

    public function reorder(Request $request)
    {
        $this->authorize('reorder', Task::class);

        $request->validate([
            'tasks' => ['required', 'array']
        ]);

        $this->taskService->reorder(
            $request->user(),
            $request->tasks
        );

        return response()->json(['message' => 'Reordered']);
    }
}
