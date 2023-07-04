<?php

namespace Ensibuuko\Flexipay\Tests;

use Faker\Factory;
use Faker\Generator;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected Generator $faker;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
    }
    /**
     * This method mocks \GuzzleHttp\Client
     * @param string $method
     * @param string $url
     * @param array $options
     * @param $responseBody
     * @param int $responseStatus
     * @param array $responseHeaders
     * @return Client|LegacyMockInterface|MockInterface
     */
    protected function mockHttpClient(string $method, string $url, array $options, $responseBody, int $responseStatus = 200, array $responseHeaders = []): MockInterface|LegacyMockInterface|Client
    {
        if(is_array($responseBody)) {
            $responseBody = json_encode($responseBody);
        }
        
        $method = strtoupper($method);

        $response = new Response($responseStatus, $responseHeaders, $responseBody);

        $client = Mockery::mock(Client::class);

        $client->shouldReceive('request')
            ->with($method, $url, $options)
            ->once()
            ->andReturn($response);
        
        return $client;
    }
}