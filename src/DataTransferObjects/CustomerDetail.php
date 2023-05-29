<?php

namespace Ensibuuko\Flexipay\DataTransferObjects;

class CustomerDetail
{
    public function __construct(
        public string $cardNumber,
        public string $mobileNumber,
        public string $firstName,
        public string $lastName,
        public string|null $secondName,
        public string $dateOfBirth,
        public string $nin,
        public Gender $gender,
        public string $occupation
    )
    {
    }
}