<?php

namespace Ensibuuko\Flexipay\Tests\Services;

use Ensibuuko\Flexipay\Services\WalletDetailsService;
use Ensibuuko\Flexipay\Tests\TestCase;
use GuzzleHttp\Client;

class FlexipayBaseServiceTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testTokenCanBeGenerated(): void
    {
        $httpClient = new Client();
        $service = new WalletDetailsService($httpClient);
        $password = $this->faker->uuid();
        $requestId = $this->faker->uuid();
        $token = $service->generateToken($password, $requestId);
        $this->assertNotNull($token);
    }
}
