<?php

namespace Ensibuuko\Flexipay\DataTransferObjects;

class SaccoOnboardingResponse
{
    public function __construct(
        public string $statusCode,
        public string $statusDesc,
        public string $requestReference
    )
    {
    }
}