<?php

namespace Tests\Feature;

use App\Models\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QueueMonitorTest extends TestCase
{
    use RefreshDatabase;

    public function test_monitor_data_returns_active_call_and_waiting_queues(): void
    {
        Queue::create([
            'queue_number' => 'A-001',
            'status' => 'called',
        ]);
        Queue::create([
            'queue_number' => 'A-002',
            'status' => 'waiting',
        ]);

        $response = $this->getJson(route('queue.data'));

        $response->assertOk()
            ->assertJsonPath('currentQueue.queue_number', 'A-001')
            ->assertJsonCount(1, 'waitingQueues')
            ->assertJsonPath('waitingQueues.0.queue_number', 'A-002')
            ->assertJsonMissingPath('stats')
            ->assertJsonMissingPath('queues');
    }
}
