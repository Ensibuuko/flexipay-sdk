<?php

namespace Ensibuuko\Flexipay\DataTransferObjects;

class SaccoOnboardingRequest
{
    /**
     * @param string $clientId
     * @param string $aggregatorId
     * @param string $password
     * @param string $saccoId
     * @param string $saccoName
     * @param string $saccoAccount
     * @param string $aggregatorId
     * @param string $requestReference
     * @param string $amount
     */
    public function __construct(
        public string $clientId,
        public string $aggregatorId,
        public string $password,
        public string $saccoId,  
        public string $saccoName,
        public string $saccoAccount,
        public string $requestReference,
        public string $amount
    )
    {
    }
}