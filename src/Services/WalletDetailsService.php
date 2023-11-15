<?php

namespace Ensibuuko\Flexipay\Services;

use Ensibuuko\Flexipay\Exceptions\SignatureGenerationException;
use Ensibuuko\Flexipay\Providers\RequestProvider;
use Ensibuuko\Flexipay\Requests\WalletDetailsRequest;
use Ensibuuko\Flexipay\Responses\WalletDetailsResponse;
use Ensibuuko\Flexipay\Exceptions\WalletDetailsException;

class WalletDetailsService extends FlexipayBaseService
{
    const FAILURE_MESSAGE = "Fetch Flexipay Wallet Details Failed: %s";

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
            'msisdn' => $request->msisdn,
            'requestId' => $request->requestId,
            'clientId' => $requestProvider->aggregatorID,
        ];
        
        $content = json_encode($payload);
        $signature = $this->generateRequestSignature($content, $requestProvider->privateKey);

        $headers = [
            'password' => $requestProvider->password,
            'X-IBM-Client-Id' => $requestProvider->clientId,
            'X-IBM-Client-Secret' => $requestProvider->clientSecret,
            'Authorization' => "Bearer {$token}",
            'x-signature' => $signature,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $url = $requestProvider->baseUrl . self::SACCO_DETAILS_URI;

        $this->logger->debug(json_encode([
            'FLEXIPAY_WALLET_DETAILS_REQUEST' => [
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
            throw new WalletDetailsException($ex->getMessage(), $ex->getCode());
        }

        $contents = $response->getBody()->getContents();

        $this->logger->debug(json_encode([
            "FLEXIPAY_WALLET_DETAILS_RESPONSE" => [
                'Payload' => $contents,
                'StatusCode' => $response->getStatusCode()
            ]
        ]));

        $status = $response->getStatusCode();
        if ($status < 200 || $status > 299) {
            throw new WalletDetailsException(sprintf(self::FAILURE_MESSAGE, $contents));
        }

        $responseArray = json_decode($contents, true);

        return new WalletDetailsResponse(
            $responseArray['walletName'],
            $responseArray['currency'],
            $responseArray['allowCredit'] === 'Y',
            $responseArray['allowDebit'] === 'Y',
            $responseArray['statusDescription'],
            $responseArray['statusCode']
        );
    }
}