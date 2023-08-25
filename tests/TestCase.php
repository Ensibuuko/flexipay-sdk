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
    protected string $baseUrl = "http://127.0.0.1";
    protected string $aggregatorId = "ENS";
    protected string $clientId;
    protected string $clientSecret;
    protected string $password;
    protected string $privateKey = "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQChhB70LfMOZQ+4\nrMiyjqfYh1XwqOeqLZBUqRdYDKPlSGwpWfYuKhgkQfjmWTihk3vsOHtWJ47xziIU\nj98iy3Mme7aIry6g6FVybeveKdEI9KrLNwZyQlSwa6g7LQqUgO0IaF7IRrqqt/NQ\nsWSiWjFVBIToauk5k42w37SJhFweNMq5IYTWrD4LbAxMVulQ46e3g3jBPSpJzmeo\nk891v2Sp32GPqYbtMk5MHpepjxSpg6N2KdL5Km/hPTXnNa03pxsYInWpsOVZL77t\nIwH827AOGMZiAs8EHI28O/2Gx/Rpna0IFR+HhyN92GBWgdWtczAONpFtdTZ3kAj2\nD6OpHnHNAgMBAAECggEAAN6N7xkHN6LeHouZifKAH0d9NC4ojw8vOXFujXwF7tBA\nusk/7LgvyJQtU4oT5rS9RER6j3hnidAm+U7b79tB+6lLTnmI6fdOLG9Ah4HYnPB6\nMd1SNM+8FnYB4g2oQqvd+r9bET9mNlCGp/7IcKJFJTTAxWJ77mInr1XI6juS5lu2\nshZKwVEGHU3pGWJ0mD2hwh6MUeCA79CZjkLagBqpwmjdTjRUurAnJmj/RkMQTGG6\n2IOZ5XhgLH8y4l2zaR4PD8ZfGJdjo31+tY64SqFEkCMjcsXCKP+pru5ZCj6+P/LS\nAdXtHSMI/+dFvZ5c+U3QBuEm7RB7pBRd7/hH3QxjUQKBgQDQLKrUJk68HSgizh23\naLs4ln8vmYqhynwGK2XRue2pJNTOMcYM5NyN+ThdP9I/w9zz1m3gahwHrqWSApzl\nrEBunWtCyGj3yHHAUhlJfKJhyMBF0Id6D7rJlz8OodvJShMHlWh3dLOIiTpJVBZq\nrnznYePeUE36VZ8K1+CifhO0fQKBgQDGn1dqwk5RQBKh+euOqE+fwHRF8ZPTMoy2\n3NwoOCmk+CkU2+JvrVeAagQHyXg06fwoGVT1l2LZgOHAJjabC1UgWngneg6cMKr7\nHs8L4w+eK2+p0WCHYy8DwkRBvbWPrbuqO7++WAZ9EFxOm0qDAUtRD54iiYiyMv1/\nx00PzoLDkQKBgQCNNj+LOZEfOggH1BmEWJ7ne+86ssS/i0MmTDn9UkpM+pcAZ/MY\nvAaAqBX761cLuikfns1Z9aCd9XKK4QrdIUNzxYUFAD10F5xSAV714n2kJzGGKjY8\nLn9eUCKOIm2c1YqjEk6S+a7vZ2cKZuft5f8EVfrky0SaE4qXKGlQ0IU9YQKBgFru\n0GK8GobwQpeNh96ECBAnWBQ9iWQDnJCLhO+U5tv0ETrPgFeIkKrl3nqCLlprVVo2\nXsMhT8wsSS5jUFSjV5G8WY4ZP18amOznKftTNO9BC5o/lWXmbrvV0NIYPGtPKr/B\nIwPN4QqY1unWBsL7cLPn5ooBVWfgGFbLs+gsr4lBAoGAd8HJvY+zpO9ljob0P7tP\nWvPzB/2GZktZlZjWQbZf6lGTamQxUdiRoeVCCWQshYnMXz0VrBCYy/VHzTDs49as\npjttLCXEtqx1T772rdUMtvGU6VJG6H2Wbkr6BvgLngnQ7FbZ9/mSHOfsM0ywTWwL\nFsisw9ghoUNoKt09y8uUgXw=\n-----END PRIVATE KEY-----\n";

    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
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