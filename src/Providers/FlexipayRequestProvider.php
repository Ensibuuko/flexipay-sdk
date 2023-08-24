<?php

namespace Ensibuuko\Flexipay\Providers;

class FlexipayRequestProvider
{
    public function __construct(
        public string $baseUrl,
        public string $clientId,
        public string $password,
    )
    {
    }
}