<?php

namespace Ensibuuko\Flexipay\Services;

use Ensibuuko\Flexipay\Exceptions\FetchTokenException;
use Ensibuuko\Flexipay\Exceptions\SignatureGenerationException;
use Ensibuuko\Flexipay\Providers\RequestProvider;
use GuzzleHttp\Client;

abstract class FlexipayBaseService
{
    const HASH_ALGO = 'sha256';
    public const TOKEN_URI = "/ug/oauth2/token";
    public const SACCO_ONBOARDING_URI = "/fp/v1.0/Onboarding";
    public const MEMBER_ONBOARDING_URI = "/flexipayws/v1.0/registration/api";
    public const SACCO_DETAILS_URI = "/flexipayws/v1.0/payments/getwalletdetails";
    const SIGNATURE_GENERATION_ERROR_MESSAGE = "Could not generate signature: %s";

    public function __construct(public Client $httpClient) { }

    /**
     * @param RequestProvider $requestProvider
     * @return string
     * @throws FetchTokenException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function generateToken(RequestProvider $requestProvider): string
    {
        $hash = base64_encode("{$requestProvider->clientId}:{$requestProvider->clientSecret}");
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => "Basic {$hash}",
        ];

        $url = $requestProvider->baseUrl . self::TOKEN_URI;

        $response = $this->httpClient->request("POST", $url, [
            'headers' => $headers,
            'params' => [
                'grant_type' => 'client_credentials',
                'scope' => 'Create'
            ]
        ]);

        $contents = json_decode($response->getBody()->getContents(), true);

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() <= 299) {
            return $contents['access_token'];
        }
        
        throw new FetchTokenException($response->getBody()->getContents());
    }

    /**
     * @param string $content
     * @param string $privateKey
     * @return string
     * @throws SignatureGenerationException
     */
    public function generateRequestSignature(string $content, string $privateKey): string
    {
        $signature = '';
        if (openssl_sign($content, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
            return base64_encode($signature);
        }
        $message = sprintf(self::SIGNATURE_GENERATION_ERROR_MESSAGE, openssl_error_string());
        throw new SignatureGenerationException($message);
    }
}