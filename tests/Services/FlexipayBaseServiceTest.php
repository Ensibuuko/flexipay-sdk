<?php

namespace Ensibuuko\Flexipay\Tests\Services;

use Ensibuuko\Flexipay\Exceptions\SignatureGenerationException;
use Ensibuuko\Flexipay\Providers\RequestProvider;
use Ensibuuko\Flexipay\Services\FlexipayBaseService;
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
        $privateKey = $this->faker->uuid();
        $requestProvider = new RequestProvider($this->baseUrl, $this->aggregatorId, $this->clientId, $this->clientSecret, $this->password, $privateKey);
        $url = $this->baseUrl . FlexipayBaseService::TOKEN_URI;
        $hash = base64_encode("{$this->clientId}:{$this->clientSecret}");
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => "Basic {$hash}",
        ];
        $options = [
            'headers' => $headers,
            'form_params' => [
                'grant_type' => 'client_credentials',
                'scope' => 'Create'
            ]
        ];
        $mockedResponse = [
            "token_type" => "Bearer",
            "access_token" => $this->faker->uuid(),
            "scope" => "Create",
            "expires_in" => 900,
            "consented_on" => (new \DateTime())->getTimestamp()
        ];
        $httpClient = $this->mockHttpClient("POST", $url, $options, $mockedResponse, 200);
        $service = new WalletDetailsService($httpClient);
        $token = $service->generateToken($requestProvider);
        $this->assertNotNull($token);
        $this->assertEquals($mockedResponse['access_token'], $token);
    }

    public function testGenerateSignatureThrowsAnExceptionWhenPrivateKeyIsNotValid(): void
    {
        $httpClient = new Client();
        $service = new WalletDetailsService($httpClient);
        $content = random_bytes(64);
        $privateKey = random_bytes(16);
        $this->expectException(SignatureGenerationException::class);
        $service->generateRequestSignature($content, $privateKey);
    }

    public function testSignatureCanBeGenerated(): void
    {
        $httpClient = new Client();
        $service = new WalletDetailsService($httpClient);
        $content = random_bytes(64);
        $token = $service->generateRequestSignature($content, $this->privateKey);
        $this->assertNotNull($token);
    }
}
