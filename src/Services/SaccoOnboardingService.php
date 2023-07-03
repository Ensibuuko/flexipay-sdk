<?php

namespace Ensibuuko\Flexipay\Services;

use Ensibuuko\Flexipay\DataTransferObjects\FlexipayRequestProvider;
use Ensibuuko\Flexipay\DataTransferObjects\SaccoOnboardingRequest;
use Ensibuuko\Flexipay\DataTransferObjects\SaccoOnboardingResponse;
use Ensibuuko\Flexipay\Exceptions\SaccoOnboardingException;
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
     * @throws SaccoOnboardingException
     */
    public function onboard(
        SaccoOnboardingRequest $request,
        FlexipayRequestProvider $requestProvider
    ): SaccoOnboardingResponse
    {
        $token = $this->generateToken(
            $request->clientId,
            $request->aggregatorId,
            $request->password,
            $request->saccoId ,
            $request->requestReference,
            0
        );
        $content = "";
        $signature = $this->generateRequestSignature(
            $content,
            $requestProvider->privateKey,
            $requestProvider->privateKeyAlias
        );

        $headers = [
            'token' => $token,
            'signature' => $signature,
        ];

        $payload = [
            'saccoId' => $request->saccoId,
            'saccoName' => $request->saccoName,
            'saccoAccount' => $request->saccoAccount,
            'aggregatorID' => $request->aggregatorId,
            'requestReference' => $request->requestReference,
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