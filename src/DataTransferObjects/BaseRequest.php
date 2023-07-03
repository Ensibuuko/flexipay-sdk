<?php

namespace Ensibuuko\Flexipay\DataTransferObjects;

class BaseRequest
{
    /**
     * @param string $clientId
     * @param string $aggregatorId
     * @param string $password
     */
    public function __construct(
        public string $clientId,
        public string $aggregatorId,
        public string $password,
    )
    {
    }
}