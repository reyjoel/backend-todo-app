<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Task API', function () {

    it('can create a task', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/tasks', [
            'statement' => 'Test task',
            'task_date' => now()->toDateString(),
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'statement' => 'Test task'
            ]);

        $this->assertDatabaseHas('tasks', [
            'statement' => 'Test task',
            'user_id' => $user->id
        ]);
    });

    it('can update a task', function () {
        $user = User::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)
            ->putJson("/api/tasks/{$task->id}", [
                'statement' => 'Updated task'
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'statement' => 'Updated task'
            ]);
    });

    it('can delete a task', function () {
        $user = User::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id
        ]);
    });

    it('cannot update another users task', function () {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $user1->id
        ]);

        $response = $this->actingAs($user2)
            ->putJson("/api/tasks/{$task->id}", [
                'statement' => 'Hacked'
            ]);

        $response->assertStatus(403);
    });

    it('fails when statement is missing', function () {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/tasks', [])
            ->assertStatus(422);
    });

    it('can toggle task completion', function () {
        $user = User::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $user->id,
            'is_completed' => false
        ]);

        $this->actingAs($user)
            ->patchJson("/api/tasks/{$task->id}/toggle")
            ->assertStatus(200);

        expect($task->fresh()->is_completed)->toBeTrue();
    });

    it('can search tasks', function () {
        $user = User::factory()->create();

        Task::factory()->create([
            'user_id' => $user->id,
            'statement' => 'Call client',
            'task_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/tasks?q=Call&date=' . now()->toDateString());

        $response->assertStatus(200)
            ->assertJsonFragment([
                'statement' => 'Call client'
            ]);
    });

    it('can filter tasks by date', function () {
        $user = User::factory()->create();

        Task::factory()->create([
            'user_id' => $user->id,
            'statement' => 'Today task',
            'task_date' => now()->toDateString(),
        ]);

        Task::factory()->create([
            'user_id' => $user->id,
            'statement' => 'Other day',
            'task_date' => now()->subDay()->toDateString(),
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/tasks?date=' . now()->toDateString());

        $response->assertStatus(200)
            ->assertJsonFragment(['statement' => 'Today task'])
            ->assertJsonMissing(['statement' => 'Other day']);
    });

    it('cannot see other users tasks in index', function () {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Task::factory()->create([
            'user_id' => $user1->id,
            'statement' => 'Private task',
            'task_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($user2)
            ->getJson('/api/tasks?date=' . now()->toDateString());

        $response->assertStatus(200)
            ->assertJsonMissing(['statement' => 'Private task']);
    });

    it('returns paginated tasks', function () {
        $user = User::factory()->create();

        Task::factory()->count(25)->create([
            'user_id' => $user->id,
            'task_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/tasks?date=' . now()->toDateString());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta'
            ]);
    });

    it('cannot delete another users task', function () {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $user1->id
        ]);

        $this->actingAs($user2)
            ->deleteJson("/api/tasks/{$task->id}")
            ->assertStatus(403);
    });

    it('can reorder tasks', function () {
        $user = User::factory()->create();

        $tasks = Task::factory()->count(3)->create([
            'user_id' => $user->id
        ]);

        $orderedIds = $tasks->pluck('id')->reverse()->values()->toArray();

        $this->actingAs($user)
            ->postJson('/api/tasks/reorder', [
                'tasks' => $orderedIds
            ])
            ->assertStatus(200);

        foreach ($orderedIds as $index => $id) {
            $this->assertDatabaseHas('tasks', [
                'id' => $id,
                'position' => $index
            ]);
        }
    });

});
