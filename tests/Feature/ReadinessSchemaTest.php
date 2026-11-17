<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ReadinessSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_does_not_check_migrations_as_part_of_readiness(): void
    {
        DB::table('migrations')
            ->where('migration', '2026_11_03_000001_enable_rls_on_application_tables')
            ->delete();

        $this->get('/api/readyz')
            ->assertOk()
            ->assertSeeText('ready');
    }
}
