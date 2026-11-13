<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrustedProxyHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_forwarded_host_and_scheme_do_not_override_the_request_origin(): void
    {
        $response = $this->withServerVariables([
            'REMOTE_ADDR' => '127.0.0.1',
            'HTTP_HOST' => 'micro-commerce.test',
            'HTTP_X_FORWARDED_HOST' => 'attacker.example',
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'SERVER_PORT' => '80',
        ])->get('/');

        $response->assertOk();
        $this->assertStringNotContainsString('attacker.example', $response->getContent());
    }
}
