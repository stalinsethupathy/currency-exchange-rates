<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\CurrencyFetcher;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class CurrencyFetcherTest extends TestCase
{
    public function testCanFetchUsdRates()
    {
        $mockClient = $this->createMock(Client::class);
        $mockResponse = new Response(200, [], json_encode(['quotes' => ['USDAED' => 3.67]]));

        $mockClient->method('get')->willReturn($mockResponse);

        $fetcher = new CurrencyFetcher($mockClient);
        $rates = $fetcher->getRates('USD');

        $this->assertIsArray($rates);
        $this->assertArrayHasKey('USDAED', $rates);
    }

    public function testCannotFetchInvalidCurrencyRates()
    {
        $mockClient = $this->createMock(Client::class);
        $mockResponse = new Response(200, [], json_encode(['quotes' => []])); 

        $mockClient->method('get')->willReturn($mockResponse);

        $fetcher = new CurrencyFetcher($mockClient);
        $rates = $fetcher->getRates('XYZ');
        $this->assertIsArray($rates);
        $this->assertEmpty($rates);
    }
}
