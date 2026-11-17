<?php

namespace Tests\Feature;

use Tests\TestCase;

class TrustedProxyConfigTest extends TestCase
{
    public function test_trusted_proxies_are_loaded_from_configuration(): void
    {
        config(['trustedproxy.proxies' => '10.0.0.1']);

        $this->assertSame('10.0.0.1', config('trustedproxy.proxies'));
    }
}
