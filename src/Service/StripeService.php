<?php
namespace App\Service;

class StripeService
{
    private $secretKey;
    private $publishableKey;

    public function __construct(string $secretKey, string $publishableKey)
    {
        $this->secretKey = $secretKey;
        $this->publishableKey = $publishableKey;
    }

    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    public function getPublishableKey(): string
    {
        return $this->publishableKey;
    }
}
