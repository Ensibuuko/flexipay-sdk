<?php

namespace Ensibuuko\Flexipay\DataTransferObjects;

enum Gender: string
{
    case MALE = "MALE";
    case FEMALE = "FEMALE";
    case OTHER = "OTHER";
}