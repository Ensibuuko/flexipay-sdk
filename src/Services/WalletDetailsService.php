<?php

namespace Ensibuuko\Flexipay\Services;

use Ensibuuko\Flexipay\Providers\FlexipayRequestProvider;
use Ensibuuko\Flexipay\Requests\WalletDetailsRequest;
use Ensibuuko\Flexipay\Responses\WalletDetailsResponse;
use Ensibuuko\Flexipay\Exceptions\SignatureGenerationException;
use Ensibuuko\Flexipay\Exceptions\WalletDetailsException;
use GuzzleHttp\Client;

class WalletDetailsService extends FlexipayBaseService
{
    const WALLET_DETAILS_URI = "/flexipayws/v1.0/payments/getwalletdetails";
    const FAILURE_MESSAGE = "Fetch Wallet Details Failed: %s";

    public function __construct(public Client $httpClient) { }

    /**
     * @param WalletDetailsRequest $request
     * @param FlexipayRequestProvider $requestProvider
     * @return WalletDetailsResponse
     * @throws SignatureGenerationException
     * @throws WalletDetailsException
     */
    public function fetchWalletDetails(
        WalletDetailsRequest    $request,
        FlexipayRequestProvider $requestProvider
    ): WalletDetailsResponse
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

        $url = $requestProvider->baseUrl . self::WALLET_DETAILS_URI;

        $payload = [
            'MSISDN' => $request->msisdn,
            'REQUEST_ID' => $request->requestId,
            'CLIENT_ID' => $requestProvider->clientId,
        ];

        try {
            $response = $this->httpClient->request('POST', $url, [
                'json' => $payload,
                'headers' => $headers
            ]);
        } catch (\Throwable $ex) {
            throw new WalletDetailsException($ex->getMessage(), $ex->getCode());
        }

        $contents = $response->getBody()->getContents();

        $status = $response->getStatusCode();
        if ($status < 200 || $status > 299) {
            throw new WalletDetailsException(sprintf(self::FAILURE_MESSAGE, $contents));
        }

        $responseArray = json_decode($contents, true);

        return new WalletDetailsResponse(
            $responseArray['WALLET_NAME'],
            $responseArray['CURRENCY'],
            $responseArray['ALLOW_CR'] === 'Y',
            $responseArray['ALLOW_DR'] === 'Y',
            $responseArray['STATUSMESSAGE'],
            $responseArray['STATUS']
        );
    }
}