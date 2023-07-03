<?php

namespace Ensibuuko\Flexipay\Requests;

class BaseRequest
{
    /**
     * @param string $clientId
     * @param string $aggregatorId
     * @param string $password
     * @param string $saccoId
     */
    public function __construct(
        public string $clientId,
        public string $aggregatorId,
        public string $password,
        public string $saccoId,
    )
    {
    }
}