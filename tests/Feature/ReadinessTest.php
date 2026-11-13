<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_ok_when_the_database_connection_and_migrations_are_ready(): void
    {
        $this->get('/api/readyz')
            ->assertOk()
            ->assertSeeText('ready');
    }

    public function test_it_returns_service_unavailable_when_the_database_query_fails(): void
    {
        DB::shouldReceive('select')
            ->once()
            ->with('select 1')
            ->andThrow(new \RuntimeException('database unavailable'));

        $this->get('/api/readyz')
            ->assertServiceUnavailable()
            ->assertSeeText('unavailable');
    }
}
