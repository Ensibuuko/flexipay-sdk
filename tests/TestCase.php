<?php

namespace Ensibuuko\Flexipay\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * This method mocks \GuzzleHttp\Client
     * @param $method
     * @param $url
     * @param $options
     * @param $responseBody
     * @param int $responseStatus
     * @param array $responseHeaders
     * @return Client|\Mockery\LegacyMockInterface|\Mockery\MockInterface
     */
    protected function mockHttpClient($method, $url, $options, $responseBody, $responseStatus = 200,  $responseHeaders = []): \Mockery\MockInterface|\Mockery\LegacyMockInterface|Client
    {
        if(is_array($responseBody)) {
            $responseBody = json_encode($responseBody);
        }
        
        $method = strtoupper($method);

        $response = new Response($responseStatus, $responseHeaders, $responseBody);

        $client = \Mockery::mock(\GuzzleHttp\Client::class);

        $client->shouldReceive('request')
            ->with($method, $url, $options)
            ->once()
            ->andReturn($response);
        
        return $client;
    }
}