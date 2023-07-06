<?php

namespace Ensibuuko\Flexipay\Services;

use Ensibuuko\Flexipay\Providers\FlexipayRequestProvider;
use Ensibuuko\Flexipay\Requests\SaccoOnboardingRequest;
use Ensibuuko\Flexipay\Responses\SaccoOnboardingResponse;
use Ensibuuko\Flexipay\Exceptions\SaccoOnboardingException;
use Ensibuuko\Flexipay\Exceptions\SignatureGenerationException;
use GuzzleHttp\Client;

class SaccoOnboardingService extends FlexipayBaseService
{
    const SACCO_ONBOARDING_URI = "/FLEXI_SACCO_API/ONBOARDING";
    const FAILURE_MESSAGE = "Sacco Onbaording Failed: %s";

    public function __construct(
        public Client $httpClient
    )
    {
    }

    /**
     * @param SaccoOnboardingRequest $request
     * @param FlexipayRequestProvider $requestProvider
     * @return SaccoOnboardingResponse
     * @throws SaccoOnboardingException
     * @throws SignatureGenerationException
     */
    public function onboard(
        SaccoOnboardingRequest  $request,
        FlexipayRequestProvider $requestProvider
    ): SaccoOnboardingResponse
    {
        $token = $this->generateToken(
            $requestProvider->clientId,
            $requestProvider->password,
            $request->saccoId,
            $request->requestId,
            0
        );
        $content = "";
        $signature = $this->generateRequestSignature(
            $content,
            $requestProvider->privateKey
        );

        $headers = [
            'saccoId' => $request->saccoId,
            'password' => $requestProvider->password,
            'client_ID' => $requestProvider->clientId,
            'token' => $token,
            'signature' => $signature,
        ];

        $payload = [
            'saccoId' => $request->saccoId,
            'saccoName' => $request->saccoName,
            'saccoAccount' => $request->saccoAccount,
            'aggregatorID' => $requestProvider->clientId,
            'requestReference' => $request->requestId,
            "Amount" => "0"
        ];

        $url = $requestProvider->baseUrl . self::SACCO_ONBOARDING_URI;

        try {
            $response = $this->httpClient->request('POST', $url, [
                'json' => $payload,
                'headers' => $headers
            ]);
        } catch (\Throwable $ex) {
            throw new SaccoOnboardingException($ex->getMessage(), $ex->getCode());
        }

        $contents = $response->getBody()->getContents();

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