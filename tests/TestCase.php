<?php

namespace Ensibuuko\Flexipay\Tests;

use Faker\Factory;
use Faker\Generator;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use Monolog\Logger;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected Generator $faker;
    protected Logger $logger;
    protected string $baseUrl = "http://127.0.0.1";
    protected string $aggregatorId = "ENS";
    protected string $clientId;
    protected string $clientSecret;
    protected string $password;
    protected string $privateKey = "-----BEGIN RSA PRIVATE KEY-----\nMIIEpAIBAAKCAQEAz+aVIAmTr0QBom50LVT3qTvmCWqJ1JmSzmd/dY2GCJjhK47T\nqYKyfbVfThm0qTuREA5VhAC1GNwQUHFE/S+OfQJUBoyQdfQVGXyFcy+m5IGCMSA+\nR6GpqmtxwMnd0KjhVv9wTd+GskydUyacvuxMuVGyVh88Bhlo9gFYzvxgTq5tHruq\nkO+Tc4Da+LvEB9uq2tbL2QMdvgXMB+C8tcCRBFtudFZph8Fyll6XwQYXoFU7bwRr\nOaRJ2WZrLLI50weS9qwmXfP2rriRb93R8ML1mwJrjX14f0ZfIW98wyICBGTj7hj2\nU5eW5BPJ32COXeC7oCYlOUoa9eTA2Wrc1lt5IQIDAQABAoIBAHLN4/+DCli4dyaZ\nU/JMf06m0wrUGDSccaMlKA9kDWXl9kG1Z1Ct6Z7dbzmTnF3vlNWG9NYmBdsqep/R\nTMV7Y3XIuVm8eXGJTbV7O741zDVXBuvV4E5yCV0gY/qP/rtg5r7B4+Q80QbYo+/s\n3JXZqyyS3qYMLXs4wOtIJyB14UDQtiO6GKJmSOFZ9gyObJeAR41ET4UjP34VRCbc\nQ1tFIdJe6ESbOAL2g2LAFvAZp79eSDIzg//LTd3GHSRLSCDDLA8HVq/+nZOHRMc8\neLt4CNpn9XnUVpujDXo6dQLhnwh/AOupwkPzCKEFAFHAd3VWr4cJCN1KxMetFDIX\nQoo3aqECgYEA9ANcqxRYQVPUZaK7fAbS9/+r58Egc9R+iHAHcmKq/O23+cIthG/o\n2Ag0s1uNkPeBBsEx4v/LgwEFaC1gyONYyRFvg3qx5+SBfEkYDhdYq8SuI38IDCTP\npZ8lE4n/an17IKF48Cche/q5bUOnm70t+LB5b8S0edgb8YbEdOmC7MUCgYEA2h0U\nzj/rfVx5UbVv6VfhMPTcSBeVNy4nHZeI3CWN0zCmk/OKCrIxNRgiYcyIE0PJcUw/\nrdlkyIkKI6WiP76x2lVf6Hv4v4jG2RAvJoAnILFpmkiXoQLAVzPiqIKHNbz/vPh8\nGp50Add5G+ubrpuPy/bfLIE4ibaQmj9B8JDjGK0CgYEAn2qS4DIqdoON/9iRJDZR\nHjSq2n4R9ZqeEAUg2Fod26By0XlKw3tZY8n4pDTsCAmrAf612NfE3ZGNNsJuuM/O\n8Y2yjPNbx2RCNDCMxqf6Bj44hng3ibeC8XFHh7xgYfIYvvi/SiZGefkTq47Hsbem\nkRK6Q4r/XZsSpnilYLwuDQECgYAAzVSTQZbrGhj0HPexYvpYBL5+oV+SGWBZcoI1\nVQoFectTBfU+/tLBgDo4I9loQLqP6Rje2crOFfrskKJdG6VpySCWKUaL5cPdbnrN\n6HC8ZQqfX573H5x3daBiwEAhCXXjSwKh0m1YyGMQoDgtVzMbK+g8MKl1kC9N8eeb\nK9057QKBgQCyGn2qbGB3WxSIiFkgESqTzVhDVJNop+T6aiURU/5OJ8kcz7ojO/8E\nocgeL/mz5OvlhlJOkLP6g6kAB0VKAK4Ws6WFT3iHTAdnJK4wWw052UDgwvIt2ZhI\nI4ws14nPRXF6fRrWhpp2MbANfs4yZAOGe79IczQAdWzZ+jfka5Zdcg==\n-----END RSA PRIVATE KEY-----";
    protected string $publicKey = "-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAz+aVIAmTr0QBom50LVT3\nqTvmCWqJ1JmSzmd/dY2GCJjhK47TqYKyfbVfThm0qTuREA5VhAC1GNwQUHFE/S+O\nfQJUBoyQdfQVGXyFcy+m5IGCMSA+R6GpqmtxwMnd0KjhVv9wTd+GskydUyacvuxM\nuVGyVh88Bhlo9gFYzvxgTq5tHruqkO+Tc4Da+LvEB9uq2tbL2QMdvgXMB+C8tcCR\nBFtudFZph8Fyll6XwQYXoFU7bwRrOaRJ2WZrLLI50weS9qwmXfP2rriRb93R8ML1\nmwJrjX14f0ZfIW98wyICBGTj7hj2U5eW5BPJ32COXeC7oCYlOUoa9eTA2Wrc1lt5\nIQIDAQAB\n-----END PUBLIC KEY-----";

    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
        $this->logger = new Logger('flexipay-sdk');
        $this->clientId = $this->faker->uuid();
        $this->clientSecret = $this->faker->uuid();
        $this->password = $this->faker->password;
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
        if (is_object($responseBody) || is_array($responseBody)) {
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