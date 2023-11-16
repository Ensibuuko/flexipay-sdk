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
        $service = new WalletDetailsService($httpClient, $this->logger);
        $token = $service->generateToken($requestProvider);
        $this->assertNotNull($token);
        $this->assertEquals($mockedResponse['access_token'], $token);
    }

    public function testGenerateSignatureThrowsAnExceptionWhenPrivateKeyIsNotValid(): void
    {
        $httpClient = new Client();
        $service = new WalletDetailsService($httpClient, $this->logger);
        $content = random_bytes(64);
        $privateKey = random_bytes(16);
        $this->expectException(SignatureGenerationException::class);
        $service->generateRequestSignature($content, $privateKey);
    }

    public function testSignatureCanBeGenerated(): void
    {
        $httpClient = new Client();
        $service = new WalletDetailsService($httpClient, $this->logger);
        $payload= [
            "requestId" => "11418300335289418300",
            "requestTime" => "16/11/2023 14:37:53",
            "numberOfRecords" => 1,
            "clientId" => "TheAggregatorID",
            "narrative" => "New Member",
            "callbackURL" => "http://localhost:8080/api/v1/flexipay/member-onboarding/callback",
            "customerData" => [
                [
                    "cardNumber" => "IT38V123046258",
                    "mobileNumber" => "256773586556",
                    "dob" => "18/09/2005",
                    "firstName" => "Yoakim",
                    "secondName" => null,
                    "lastName" => "Owor",
                    "nin" => "IT38V123046258",
                    "gender" => "MALE",
                    "occupation" => "Businessman"
                ]
            ]
        ];
        $content = str_replace('\\', '', json_encode($payload));
        $expectedSignature = 'zk1ULjoqiA5xpEaJnr0XjTu8nZC7+bA+/R5Pc2E9E38fzO2KesMMBtmSPcE8g8FXWZ4OuDXgz3xVQj4LdgxU7SMUaFITuBRyGShinU+Qk7zwtUkyJGeJcpl3qlxbPZ6+TqEPUWROGraUOWl2oV6GPYrB6UVyu8D8vNz9GwWJO0BtFZuD2QOB1udQ3HUye6X9yGVh1ubRot0puA3K8z48VX5uvDBGdLcqZ/EJLSH+Mnertc3olT0IHeMCCkGlkQs7tKxZNlYsmfflF5WHzc9s4a2HtPPXcjttG0hJyyvPs5bS3+iWqLJ9rxsDZQUmLyp4/wzJRsYkMMm4HACzWXEfvg==';
        $token = $service->generateRequestSignature($content, $this->privateKey);
        $this->assertNotNull($token);
        $this->assertEquals($expectedSignature, $token);
    }
}
