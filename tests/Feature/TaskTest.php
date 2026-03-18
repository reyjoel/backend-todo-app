<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Task;
// use Illuminate\Foundation\Testing\RefreshDatabase;

// uses(RefreshDatabase::class);

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

    it('can search tasks', function () {
        $user = User::factory()->create();

        Task::factory()->create([
            'user_id' => $user->id,
            'statement' => 'Call client'
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/tasks/search?q=Call');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'statement' => 'Call client'
            ]);
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

});
