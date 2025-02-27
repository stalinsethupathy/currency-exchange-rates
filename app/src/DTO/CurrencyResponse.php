<?php
declare(strict_types=1);
namespace App\DTO;

readonly class CurrencyResponse
{
    public string $pair;
    public float $rate;
    public function __construct(string $pair, float $rate)
    {
        $this->pair = $pair;
        $this->rate = $rate;
    }
}
