<?php

namespace Ensibuuko\Flexipay\Responses;

class MemberRegistrationResponse
{
    public function __construct(public string $status, public string $statusMessage) { }
}