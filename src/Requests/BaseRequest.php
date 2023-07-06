<?php

namespace Ensibuuko\Flexipay\Requests;

class BaseRequest
{
    /**
     * @param string $saccoId
     */
    public function __construct(public string $saccoId,) { }
}