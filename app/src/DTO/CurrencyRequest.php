<?php
declare(strict_types=1);
namespace App\DTO;

readonly class CurrencyRequest
{
    public string $baseCurrency;
    public function __construct(string $baseCurrency)
    {
        $this->baseCurrency = $baseCurrency;
    }
}
