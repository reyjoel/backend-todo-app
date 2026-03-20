<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\User;
use App\Repositories\Interfaces\TaskRepositoryInterface;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaskRepository implements TaskRepositoryInterface
{
    public function getUserTasksByDate(User $user, Carbon $date, ?string $query = null): LengthAwarePaginator
    {
        return Cache::remember(
            "tasks:{$user->id}:{$date->toDateString()}",
            60,
            function () use ($user, $date, $query) {
                return Task::query()
                    ->where('user_id', $user->id)
                    ->whereDate('task_date', $date)
                    ->when(filled($query), function ($q) use ($query) {
                        $q->where('statement', 'like', "%{$query}%");
                    })
                    ->orderBy('position')
                    ->paginate(20);
            }
        );
    }

    public function create(Task $data)
    {
        return Task::create($data);
    }

    public function update($task, array $data)
    {
        $task->update($data);
        return $task;
    }

    public function delete(Task $task)
    {
        return $task->delete();
    }

    public function reorder(Task $tasks)
    {
        foreach ($tasks as $item) {
            Task::where('id', $item['id'])
                ->update(['position' => $item['position']]);
        }
    }
}