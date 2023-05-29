<?php

namespace Ensibuuko\Flexipay\DataTransferObjects;

class WalletDetailsResponse
{
    public function __construct(
        public string $walletName,
        public string $currency,
        public bool $allowCredit,
        public bool $allowDebit,
        public string $statusMessage,
        public string $status
    )
    {
    }
}