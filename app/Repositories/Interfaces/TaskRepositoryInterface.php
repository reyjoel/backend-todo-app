<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TaskRepositoryInterface
{
    public function getUserTasksByDate(User $user, Carbon $date, ?string $query = null): LengthAwarePaginator;
    public function create(Task $data);
    public function update(Task $task, array $data);
    public function delete(Task $task);
    public function reorder(Task $tasks);
}