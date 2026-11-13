<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ReadinessSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_service_unavailable_when_a_migration_is_pending(): void
    {
        DB::table('migrations')
            ->where('migration', '2026_11_03_000001_enable_rls_on_application_tables')
            ->delete();

        $this->get('/api/readyz')
            ->assertServiceUnavailable()
            ->assertSeeText('unavailable');
    }
}
