<?php

namespace Ensibuuko\Flexipay\Requests;

class BaseRequest
{
    /**
     * @param string $saccoId
     * @param string $requestId
     */
    public function __construct(
        public string $saccoId, 
        public string $requestId
    ) { }
}