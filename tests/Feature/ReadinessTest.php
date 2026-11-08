<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ReadinessTest extends TestCase
{
    public function test_it_returns_ok_when_the_database_query_succeeds(): void
    {
        DB::shouldReceive('select')->once()->with('select 1')->andReturn([]);

        $this->get('/api/readyz')
            ->assertOk()
            ->assertSeeText('ready');
    }

    public function test_it_returns_service_unavailable_when_the_database_query_fails(): void
    {
        config([
            'session.driver' => 'database',
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => 'database/missing-directory/readiness.sqlite',
        ]);

        $this->get('/api/readyz')
            ->assertServiceUnavailable()
            ->assertSeeText('unavailable');
    }
}
