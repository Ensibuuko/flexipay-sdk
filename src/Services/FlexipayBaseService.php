<?php

namespace Ensibuuko\Flexipay\Services;

abstract class FlexipayBaseService
{
    const HASH_ALGO = 'sha256';
    
    public function generateToken(
        string $clientId,
        string $aggregatorId,
        string $password,
        string $saccoId,
        string $requestId,
        int $amount
    ): string
    {
        $parsedData = "{$saccoId}{$requestId}{$aggregatorId}{$amount}";
        $hash = hash_hmac(self::HASH_ALGO, $parsedData, $password, true);
        return base64_encode($hash);
    }

    public function generateRequestSignature(
        string $content,
        string $privateKey,
        string $privateKeyAlias
    ): string
    {
        return "";
    }
}