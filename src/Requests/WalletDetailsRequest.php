<?php

namespace Ensibuuko\Flexipay\Requests;

class WalletDetailsRequest extends BaseRequest
{
    /**
     * @param string $clientId
     * @param string $aggregatorId
     * @param string $password
     * @param string $saccoId
     * @param string $requestId
     * @param string $msisdn
     */
    public function __construct(
        public string $clientId,
        public string $aggregatorId,
        public string $password,
        public string $saccoId,
        public string $requestId,
        public string $msisdn,
    )
    {
        parent::__construct($this->clientId, $this->aggregatorId, $this->password, $saccoId);
    }
}