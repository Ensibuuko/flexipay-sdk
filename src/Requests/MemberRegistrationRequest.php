<?php

namespace Ensibuuko\Flexipay\Requests;

use Ensibuuko\Flexipay\DataTransferObjects\CustomerDetail;

class MemberRegistrationRequest extends BaseRequest
{
    /**
     * @param string $saccoId
     * @param string $requestId
     * @param string $requestTime
     * @param string $narrative
     * @param string $callbackUrl
     * @param CustomerDetail[] $customerData
     */
    public function __construct(
        public string   $saccoId,
        public string   $requestId,
        public string $requestTime,
        public string   $narrative,
        public string   $callbackUrl,
        public array    $customerData
    )
    {
        parent::__construct($saccoId, $requestId);
    }
}