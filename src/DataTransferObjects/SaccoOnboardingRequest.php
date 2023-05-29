<?php

namespace Ensibuuko\Flexipay\DataTransferObjects;

class SaccoOnboardingRequest
{
    public function __construct(
        public string $saccoId,  
        public string $saccoName,
        public string $saccoAccount,
        public string $aggregatorId,
        public string $requestReference,
        public string $amount
    )
    {
    }
}