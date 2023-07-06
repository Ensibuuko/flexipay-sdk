<?php

namespace Ensibuuko\Flexipay\Requests;

use DateTime;
use Ensibuuko\Flexipay\DataTransferObjects\CustomerDetail;

class MemberRegistrationRequest extends BaseRequest
{
    /**
     * @param string $saccoId
     * @param string $requestId
     * @param DateTime $requestTime
     * @param string $narrative
     * @param string $callbackUrl
     * @param CustomerDetail[] $customerData
     */
    public function __construct(
        public string   $saccoId,
        public string   $requestId,
        public DateTime $requestTime,
        public string   $narrative,
        public string   $callbackUrl,
        public array    $customerData
    )
    {
        parent::__construct($saccoId);
    }
}