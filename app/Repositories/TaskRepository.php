<?php

namespace App\Repositories;

use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;

class TaskRepository implements TaskRepositoryInterface
{
    public function getByDate($userId, $date)
    {
        return Task::where('user_id', $userId)
            ->whereDate('task_date', $date)
            ->orderBy('position')
            ->get();
    }

    public function search($userId, $query)
    {
        return Task::where('user_id', $userId)
            ->where('statement', 'like', "%$query%")
            ->get();
    }

    public function create(array $data)
    {
        return Task::create($data);
    }

    public function update($task, array $data)
    {
        $task->update($data);
        return $task;
    }

    public function delete($task)
    {
        return $task->delete();
    }

    public function reorder(array $tasks)
    {
        foreach ($tasks as $item) {
            Task::where('id', $item['id'])
                ->update(['position' => $item['position']]);
        }
    }
}