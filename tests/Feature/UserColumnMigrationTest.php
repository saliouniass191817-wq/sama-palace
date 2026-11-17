<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserColumnMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_table_uses_normalized_snake_case_columns(): void
    {
        $columns = Schema::getColumnListing('users');

        $this->assertContains('first_name', $columns);
        $this->assertContains('last_name', $columns);
        $this->assertContains('address', $columns);
        $this->assertNotContains('firstName', $columns);
        $this->assertNotContains('lastName', $columns);
        $this->assertNotContains('adress', $columns);
    }
}
