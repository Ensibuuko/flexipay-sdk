<?php

namespace Ensibuuko\Flexipay\Services;

use Ensibuuko\Flexipay\DataTransferObjects\FlexipayRequestProvider;
use Ensibuuko\Flexipay\DataTransferObjects\MemberRegistrationRequest;
use Ensibuuko\Flexipay\DataTransferObjects\MemberRegistrationResponse;
use Ensibuuko\Flexipay\Exceptions\MemberRegistrationException;
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
     * @throws MemberRegistrationException
     */
    public function register(
        MemberRegistrationRequest $request,
        FlexipayRequestProvider $requestProvider
    ): MemberRegistrationResponse
    {
        $token = $this->generateToken(
            $request->clientId, 
            $request->aggregatorId,
            $request->password,
            $request->saccoId ,
            $request->requestId, 
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
            'RequestID' => $request->requestId,
            'Client_ID' => $request->clientId,
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
        return  new MemberRegistrationResponse(
            $responseArray['Status'], 
            $responseArray['StatusMessage']
        );
    }
}