<?php

namespace Ensibuuko\Flexipay\Requests;

class SaccoOnboardingRequest extends BaseRequest
{
    /**
     * @param string $saccoId
     * @param string $saccoName
     * @param string $saccoAccount
     * @param string $requestId
     * @param string $amount
     */
    public function __construct(
        public string $saccoId,
        public string $saccoName,
        public string $saccoAccount,
        public string $requestId,
        public string $amount
    )
    {
        parent::__construct($saccoId);
    }
}