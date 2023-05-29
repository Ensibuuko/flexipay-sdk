<?php

namespace Ensibuuko\Flexipay\DataTransferObjects;

use DateTime;

class MemberRegistrationResponse
{
    public function __construct(
        public string $status,
        public string $statusMessage
    )
    {
    }
}