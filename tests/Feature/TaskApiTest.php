<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_paginated_tasks(): void
    {
        Task::factory()->count(15)->create();

        $res = $this->getJson('/api/tasks')
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                    ['id','title','is_done','due_date','created_at','updated_at']
                ],
                'links','meta'
        ])
        ->assertJson(fn (AssertableJson $json) =>
            $json->where('meta.current_page', 1)
            ->where('meta.per_page', 10)
            ->etc()
        );
    }

    public function test_store_creates_task_and_returns_201(): void
    {
        $payload = [
            'title' => 'Write tests',
            'is_done' => false,
            'due_date' => now()->addDay()->toDateString(),
        ];

        $this->postJson('/api/tasks', $payload)
        ->assertCreated()
        ->assertJsonPath('data.title', 'Write tests');

        $this->assertDatabaseHas('tasks', [
            'title' => 'Write tests',
            'is_done' => false,
        ]);
    }

    public function test_store_validates_title_required(): void
    {
        $this->postJson('/api/tasks', ['is_done' => true])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['title']);
    }

    public function test_show_returns_single_task(): void
    {
        $task = Task::factory()->create();

        $this->getJson(route('tasks.show', $task))
        ->assertOk()
        ->assertJsonPath('data.id', $task->id);
    }

    public function test_update_can_toggle_is_done(): void
    {
        $task = Task::factory()->create(['is_done' => false]);

        $this->putJson(route('tasks.update', $task), ['is_done' => true])
        ->assertOk()
        ->assertJsonPath('data.is_done', true);

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'is_done' => true]);
    }

    public function test_destroy_deletes_and_returns_204(): void
    {
        $task = Task::factory()->create();

        $this->deleteJson(route('tasks.destroy', $task))
        ->assertNoContent();


        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
