<?php

namespace Ensibuuko\Flexipay\Services;

use Ensibuuko\Flexipay\Providers\RequestProvider;
use Ensibuuko\Flexipay\Requests\MemberRegistrationRequest;
use Ensibuuko\Flexipay\Responses\MemberRegistrationResponse;
use Ensibuuko\Flexipay\Exceptions\MemberRegistrationException;

class MemberRegistrationService extends FlexipayBaseService
{
    const FAILURE_MESSAGE = "Member Registration Failed %s";

    /**
     * Implements bulk registration of members
     * @param MemberRegistrationRequest $request
     * @param RequestProvider $requestProvider
     * @return MemberRegistrationResponse
     * @throws MemberRegistrationException
     * @throws \Ensibuuko\Flexipay\Exceptions\FetchTokenException
     * @throws \Ensibuuko\Flexipay\Exceptions\SignatureGenerationException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function register(
        MemberRegistrationRequest $request,
        RequestProvider $requestProvider
    ): MemberRegistrationResponse
    {
        $token = $this->generateToken($requestProvider);

        $customerData = [];

        foreach ($request->customerData as $customerDetail) {
            $customer = [
                'card_no' => $customerDetail->cardNumber,
                'mobile_number' => $customerDetail->mobileNumber,
                'dob' => $customerDetail->dateOfBirth,
                'first_name' => $customerDetail->firstName,
                'second_name' => $customerDetail->secondName,
                'last_name' => $customerDetail->lastName,
                'nin' => $customerDetail->nin,
                'gender' => $customerDetail->gender->name,
                'occupation' => $customerDetail->occupation,
            ];
            $customerData[] = $customer;
        }

        $payload = [
            'Client_ID' => $requestProvider->clientId,
            'RequestID' => $request->requestId,
            'no_of_records' => count($request->customerData),
            'Narrative' => $request->narrative,
            'callback_url' => $request->callbackUrl,
            'customer_data' => $customerData,
        ];

        $content = json_encode($payload);
        $signature = $this->generateRequestSignature($content, $requestProvider->privateKey);

        $headers = [
            'saccoId' => $request->saccoId,
            'client_ID' => $requestProvider->clientId,
            'password' => $requestProvider->password,
            'Authorization' => "Bearer {$token}",
            'x-signature' => $signature
        ];

        $url = $requestProvider->baseUrl . self::MEMBER_ONBOARDING_URI;
        
        $this->logger->debug(json_encode([
            'FLEXIPAY_MEMBER_REGISTRATION_REQUEST' => [
                'Payload' => $payload,
                'URL' => $url
            ]
        ]));

        try {
            $response = $this->httpClient->request('POST', $url, [
                'json' => $payload,
                'headers' => $headers
            ]);
        } catch (\Throwable $ex) {
            throw new MemberRegistrationException($ex->getMessage(), $ex->getCode());
        }

        $contents = $response->getBody()->getContents();

        $this->logger->debug(json_encode([
            "FLEXIPAY_MEMBER_REGISTRATION_RESPONSE" => [
                'Payload' => $contents,
                'StatusCode' => $response->getStatusCode()
            ]
        ]));

        $status = $response->getStatusCode();
        if ($status < 200 || $status > 299) {
            throw new MemberRegistrationException(sprintf(self::FAILURE_MESSAGE, $contents));
        }

        $responseArray = json_decode($contents, true);
        return new MemberRegistrationResponse(
            $responseArray['Status'],
            $responseArray['StatusMessage']
        );
    }
}