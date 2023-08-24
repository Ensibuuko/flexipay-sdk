<?php

namespace Ensibuuko\Flexipay\Services;

abstract class FlexipayBaseService
{
    const HASH_ALGO = 'sha256';

    public function generateToken(string $password, string $requestId): string
    {
        $parsedData = "{$requestId}|{$password}";
        $base64Data = base64_encode($parsedData);
        $messageDigest = hash(self::HASH_ALGO, hex2bin($base64Data));
        $number = '0x' . $messageDigest;
        return str_pad($number, 64, '0', STR_PAD_LEFT);
    }
}