<?php

namespace Ensibuuko\Flexipay\DataTransferObjects;

class CustomerDetail
{
    const GENDER_MALE = 'MALE';
    const GENDER_FEMALE = 'FEMALE';
    const GENDER_OTHER = 'OTHER';
    
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