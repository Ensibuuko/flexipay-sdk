<?php

namespace Ensibuuko\Flexipay\Providers;

class RequestProvider
{
    public function __construct(
        public string $baseUrl,
        public string $clientId,
        public string $clientSecret,
        public string $password,
        public string $privateKey,
    )
    {
    }
}