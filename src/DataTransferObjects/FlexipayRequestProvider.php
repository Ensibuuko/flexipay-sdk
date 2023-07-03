<?php

namespace Ensibuuko\Flexipay\DataTransferObjects;

class FlexipayRequestProvider
{
    public function __construct(
        public string $baseUrl,
        public string $privateKey,
        public string $privateKeyAlias
    ){}
}