<?php

namespace Ensibuuko\Flexipay\Services;

use Ensibuuko\Flexipay\Providers\FlexipayRequestProvider;
use Ensibuuko\Flexipay\Requests\MemberRegistrationRequest;
use Ensibuuko\Flexipay\Responses\MemberRegistrationResponse;
use Ensibuuko\Flexipay\Exceptions\MemberRegistrationException;
use Ensibuuko\Flexipay\Exceptions\SignatureGenerationException;
use GuzzleHttp\Client;

class MemberRegistrationService extends FlexipayBaseService
{
    const MEMBER_REGISTRATION_URI = "/flexipayws/v1.0/registration/api";
    const FAILURE_MESSAGE = "Member Registration Failed %s";

    public function __construct(
        public Client $httpClient
    )
    {
    }

    /**
     * Implements bulk registration of members
     * @param MemberRegistrationRequest $request
     * @param FlexipayRequestProvider $requestProvider
     * @return MemberRegistrationResponse
     * @throws MemberRegistrationException
     * @throws SignatureGenerationException
     */
    public function register(
        MemberRegistrationRequest $request,
        FlexipayRequestProvider   $requestProvider
    ): MemberRegistrationResponse
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
            'client_ID' => $requestProvider->clientId,
            'password' => $requestProvider->password,
            'token' => $token,
            'signature' => $signature,
        ];

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

        $url = $requestProvider->baseUrl . self::MEMBER_REGISTRATION_URI;

        try {
            $response = $this->httpClient->request('POST', $url, [
                'json' => $payload,
                'headers' => $headers
            ]);
        } catch (\Throwable $ex) {
            throw new MemberRegistrationException($ex->getMessage(), $ex->getCode());
        }

        $contents = $response->getBody()->getContents();

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