<?php

namespace Ensibuuko\Flexipay\Services;

use Ensibuuko\Flexipay\Exceptions\SignatureGenerationException;

abstract class FlexipayBaseService
{
    const HASH_ALGO = 'sha256';
    const SIGNATURE_GENERATION_ERROR_MESSAGE = "Could not generate signature: %s";

    public function generateToken(
        string $clientId,
        string $password,
        string $saccoId,
        string $requestId,
        int    $amount
    ): string
    {
        $parsedData = "{$saccoId}|{$requestId}|{$clientId}|{$amount}";
        $hash = hash_hmac(self::HASH_ALGO, $parsedData, $password, true);
        return base64_encode($hash);
    }

    /**
     * @param string $content
     * @param string $privateKey
     * @return string
     * @throws SignatureGenerationException
     */
    public function generateRequestSignature(string $content, string $privateKey): string
    {
        $signature = '';
        if (openssl_sign($content, $signature, $privateKey, OPENSSL_ALGO_SHA1)) {
            return base64_encode($signature);
        }
        $message = sprintf(self::SIGNATURE_GENERATION_ERROR_MESSAGE, openssl_error_string());
        throw new SignatureGenerationException($message);
    }
}