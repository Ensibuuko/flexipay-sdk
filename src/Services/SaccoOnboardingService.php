<?php

namespace Ensibuuko\Flexipay\Services;

use Ensibuuko\Flexipay\Exceptions\SignatureGenerationException;
use Ensibuuko\Flexipay\Providers\RequestProvider;
use Ensibuuko\Flexipay\Requests\SaccoOnboardingRequest;
use Ensibuuko\Flexipay\Responses\SaccoOnboardingResponse;
use Ensibuuko\Flexipay\Exceptions\SaccoOnboardingException;

class SaccoOnboardingService extends FlexipayBaseService
{
    const FAILURE_MESSAGE = "Sacco Onbaording Failed: %s";

    /**
     * @param SaccoOnboardingRequest $request
     * @param RequestProvider $requestProvider
     * @return SaccoOnboardingResponse
     * @throws SaccoOnboardingException
     * @throws SignatureGenerationException
     * @throws \Ensibuuko\Flexipay\Exceptions\FetchTokenException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function onboard(SaccoOnboardingRequest $request, RequestProvider $requestProvider): SaccoOnboardingResponse
    {
        $token = $this->generateToken($requestProvider);
        
        $payload = [
            "saccoId" => $request->saccoId,
            "saccoName" => $request->saccoName,
            "saccoAccount" => $request->saccoAccount,
            "aggregatorID" => $requestProvider->aggregatorID,
            "requestReference" => $request->requestId,
            "Amount" => "0"
        ];

        $signature = $this->generateRequestSignature(json_encode($payload), $requestProvider->privateKey);

        $headers = [
            'password' => $requestProvider->password,
            'X-IBM-Client-Id' => $requestProvider->clientId,
            'X-IBM-Client-Secret' => $requestProvider->clientSecret,
            'Authorization' => "Bearer {$token}",
            'x-signature' => $signature,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $url = $requestProvider->baseUrl . self::SACCO_ONBOARDING_URI;

        $this->logger->debug(json_encode([
            'FLEXIPAY_SACCO_ONBOARDING_REQUEST' => [
                'Payload' => $payload,
                'Signature' => $signature,
                'URL' => $url
            ]
        ]));

        try {
            $response = $this->httpClient->request('POST', $url, [
                'json' => $payload,
                'headers' => $headers
            ]);
        } catch (\Throwable $ex) {
            throw new SaccoOnboardingException($ex->getMessage(), $ex->getCode());
        }

        $contents = $response->getBody()->getContents();

        $this->logger->debug(json_encode([
            "FLEXIPAY_SACCO_ONBOARDING_RESPONSE" => [
                'Payload' => $contents,
                'StatusCode' => $response->getStatusCode()
            ]
        ]));

        $status = $response->getStatusCode();
        if ($status < 200 || $status > 299) {
            throw new SaccoOnboardingException(sprintf(self::FAILURE_MESSAGE, $contents));
        }

        $responseArray = json_decode($contents, true);

        return new SaccoOnboardingResponse(
            $responseArray['StatusCode'],
            $responseArray['StatusDesc'],
            $responseArray['requestReference']
        );
    }
}