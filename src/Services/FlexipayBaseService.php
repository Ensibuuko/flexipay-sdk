<?php

namespace Ensibuuko\Flexipay\Services;

abstract class FlexipayBaseService
{
    public function generateToken(
        string $clientId
    ): string
    {
        return "";
    }

    public function generateRequestSignature(
        string $content,
        string $privateKey,
        string $privateKeyAlias,
        string $privateKeyFilePath
    ): string
    {
        return "";
    }
}