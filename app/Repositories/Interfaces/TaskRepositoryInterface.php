<?php

namespace App\Repositories\Interfaces;

interface TaskRepositoryInterface
{
    public function getByDate($userId, $date);
    public function search($userId, $query);
    public function create(array $data);
    public function update($task, array $data);
    public function delete($task);
    public function reorder(array $tasks);
}