<?php

namespace Ensibuuko\Flexipay\DataTransferObjects;

use DateTime;

class MemberRegistrationRequest
{
    /**
     * @param string $requestId
     * @param DateTime $requestTime
     * @param string $clientId
     * @param string $narrative
     * @param string $callbackUrl
     * @param CustomerDetail[] $customerData
     */
    public function __construct(
        public string $requestId,
        public DateTime $requestTime,
        public string $clientId,
        public string $narrative,
        public string $callbackUrl,
        public array $customerData
    )
    {
    }
}