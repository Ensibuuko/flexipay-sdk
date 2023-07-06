<?php

namespace Ensibuuko\Flexipay\Requests;

class WalletDetailsRequest extends BaseRequest
{
    /**
     * @param string $saccoId
     * @param string $requestId
     * @param string $msisdn
     */
    public function __construct(
        public string $saccoId,
        public string $requestId,
        public string $msisdn,
    )
    {
        parent::__construct($saccoId);
    }
}