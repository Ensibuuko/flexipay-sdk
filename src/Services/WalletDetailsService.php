<?php

namespace Ensibuuko\Flexipay\Services;

use Ensibuuko\Flexipay\Exceptions\SignatureGenerationException;
use Ensibuuko\Flexipay\Providers\RequestProvider;
use Ensibuuko\Flexipay\Requests\WalletDetailsRequest;
use Ensibuuko\Flexipay\Responses\WalletDetailsResponse;
use Ensibuuko\Flexipay\Exceptions\WalletDetailsException;

class WalletDetailsService extends FlexipayBaseService
{
    const FAILURE_MESSAGE = "Fetch Wallet Details Failed: %s";

    /**
     * @param WalletDetailsRequest $request
     * @param RequestProvider $requestProvider
     * @return WalletDetailsResponse
     * @throws WalletDetailsException
     * @throws \Ensibuuko\Flexipay\Exceptions\FetchTokenException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws SignatureGenerationException
     */
    public function fetchWalletDetails(
        WalletDetailsRequest    $request,
        RequestProvider $requestProvider
    ): WalletDetailsResponse
    {
        $token = $this->generateToken($requestProvider);
        $payload = [
            'MSISDN' => $request->msisdn,
            'REQUEST_ID' => $request->requestId,
            'CLIENT_ID' => $requestProvider->clientId,
        ];
        
        $content = json_encode($payload);
        $signature = $this->generateRequestSignature($content, $requestProvider->privateKey);

        $headers = [
            'saccoId' => $request->saccoId,
            'password' => $requestProvider->password,
            'Authorization' => "Bearer {$token}",
            'x-signature' => $signature,
        ];

        $url = $requestProvider->baseUrl . self::SACCO_DETAILS_URI;

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