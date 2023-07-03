<?php

namespace Ensibuuko\Flexipay\DataTransferObjects;

use DateTime;

class MemberRegistrationRequest extends BaseRequest
{
    /**
     * @param string $clientId
     * @param string $aggregatorId
     * @param string $password
     * @param string $saccoId
     * @param string $requestId
     * @param DateTime $requestTime
     * @param string $narrative
     * @param string $callbackUrl
     * @param CustomerDetail[] $customerData
     */
    public function __construct(
        public string $clientId,
        public string $aggregatorId,
        public string $password,
        public string $saccoId,
        public string $requestId,
        public DateTime $requestTime,
        public string $narrative,
        public string $callbackUrl,
        public array $customerData
    )
    {
        parent::__construct($this->clientId, $this->aggregatorId, $this->password);
    }
}