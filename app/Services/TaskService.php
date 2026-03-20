<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TaskService
{
    public function create(User $user, array $data): Task
    {
        return Task::create([
            ...$data,
            'user_id' => $user->id,
        ]);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);
        return $task->refresh();
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }

    public function toggle(Task $task): Task
    {
        $task->update([
            'is_completed' => !$task->is_completed
        ]);

        return $task->refresh();
    }

    public function reorder(User $user, array $taskIds): void
    {
        DB::transaction(function () use ($user, $taskIds) {
            foreach ($taskIds as $index => $taskId) {
                Task::where('id', $taskId)
                    ->where('user_id', $user->id)
                    ->update(['position' => $index]);
            }
        });
    }
}