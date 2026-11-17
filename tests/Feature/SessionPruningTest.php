<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SessionPruningTest extends TestCase
{
    use RefreshDatabase;

    public function test_expired_database_sessions_are_pruned_without_deleting_active_sessions(): void
    {
        $now = now()->getTimestamp();
        $expiredId = 'expired-session';
        $activeId = 'active-session';

        DB::table('sessions')->insert([
            [
                'id' => $expiredId,
                'last_activity' => now()->subMinutes(config('session.lifetime') + 1)->getTimestamp(),
                'payload' => '',
            ],
            [
                'id' => $activeId,
                'last_activity' => $now,
                'payload' => '',
            ],
        ]);

        $this->artisan('sessions:prune-expired')->assertExitCode(0);

        $this->assertDatabaseMissing('sessions', ['id' => $expiredId]);
        $this->assertDatabaseHas('sessions', ['id' => $activeId]);
    }
}
