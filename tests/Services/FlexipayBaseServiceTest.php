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

    public function testTokenCanBeGenerated() : void
    {
        $httpClient = new Client();
        $service = new WalletDetailsService($httpClient);
        $clientId = random_bytes(16);
        $aggregatorId = random_bytes(16);
        $password = random_bytes(16);
        $saccoId = random_bytes(16);
        $requestId = random_bytes(16);
        $token = $service->generateToken($clientId, $aggregatorId, $password, $saccoId, $requestId, 0);
        $this->assertNotNull($token);
    }
}
