<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\TaskRepositoryInterface;


class TaskController extends Controller
{
    public function __construct(private TaskRepositoryInterface $taskRepo) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tasks = $this->taskRepo->getByDate(
            $request->user()->id,
            $request->date
        );

        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $task = $this->taskRepo->create($data);

        return new TaskResource($task);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $task = $this->taskRepo->update($task, $request->validated());

        return new TaskResource($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $this->taskRepo->delete($task);

        return response()->json(['message' => 'Deleted']);
    }

    public function toggle(Task $task)
    {
        $this->authorize('update', $task);

        $task->update([
            'is_completed' => !$task->is_completed
        ]);

        return new TaskResource($task);
    }

    public function search(Request $request)
    {
        $tasks = $this->taskRepo->search(
            $request->user()->id,
            $request->q
        );

        return TaskResource::collection($tasks);
    }

    public function reorder(Request $request)
    {
        $this->taskRepo->reorder($request->tasks);

        return response()->json(['message' => 'Reordered']);
    }
}
