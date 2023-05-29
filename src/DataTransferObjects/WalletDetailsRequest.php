<?php

namespace Ensibuuko\Flexipay\DataTransferObjects;

class WalletDetailsRequest
{
    public function __construct(
        public string $msisdn,
        public string $requestId,
        public string $clientId,
    )
    {
    }
}