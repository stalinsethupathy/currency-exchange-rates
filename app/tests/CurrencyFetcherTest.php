<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\CurrencyFetcher;
use App\DTO\CurrencyRequest;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class CurrencyFetcherTest extends TestCase
{
    public function testCanFetchUsdRatesWithMockClient()
    {
        $mockClient = $this->createMock(Client::class);
        $mockResponse = new Response(200, [], json_encode(['quotes' => ['USDAED' => 3.67]]));

        $mockClient->method('get')->willReturn($mockResponse);

        $fetcher = new CurrencyFetcher($mockClient);
        $rates = $fetcher->getRates(new CurrencyRequest('USD'));

        $this->assertIsArray($rates);
        $this->assertNotEmpty($rates);

        foreach ($rates as $rate) {
            $this->assertInstanceOf(App\DTO\CurrencyResponse::class, $rate);
            $this->assertSame('USDAED', $rate->pair);
            $this->assertSame(3.67, $rate->rate);
        }
    }

    public function testCannotFetchInvalidCurrencyRatesWithMockClient()
    {
        $mockClient = $this->createMock(Client::class);
        $mockResponse = new Response(200, [], json_encode(['quotes' => []]));

        $mockClient->method('get')->willReturn($mockResponse);

        $fetcher = new CurrencyFetcher($mockClient);
        $rates = $fetcher->getRates(new CurrencyRequest('XYZ'));

        $this->assertIsArray($rates);
        $this->assertEmpty($rates);

        $mockClient = $this->createMock(Client::class);
        $mockResponse = new Response(500, [], 'Internal Server Error');

        $mockClient->method('get')->willReturn($mockResponse);

        $fetcher = new CurrencyFetcher($mockClient);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Error: Received status code 500 from API.');

        $fetcher->getRates(new CurrencyRequest('XYZ'));
    }

    public function testCanConvertCurrencyWithMockClient()
    {
        $mockClient = $this->createMock(Client::class);
        $mockResponse = new Response(200, [], json_encode(['quotes' => ['USDJPY' => 109.573]]));

        $mockClient->method('get')->willReturn($mockResponse);

        $fetcher = new CurrencyFetcher($mockClient);
        $convertedAmount = $fetcher->convertCurrency('USD', 'JPY', 100);

        $this->assertIsFloat($convertedAmount);
        $this->assertEqualsWithDelta(10957.3, $convertedAmount, 0.01);
    }

    public function testCanConvertCurrencyWithLiveClient()
    {
        $fetcher = new CurrencyFetcher();
        $convertedAmount = $fetcher->convertCurrency('USD', 'JPY', 100);

        $this->assertIsFloat($convertedAmount);
        $this->assertGreaterThan(0, $convertedAmount);

        $rates = $fetcher->getRates(new CurrencyRequest('USD'));

        $this->assertIsArray($rates);
        $this->assertNotEmpty($rates);

        foreach ($rates as $rate) {
            $this->assertInstanceOf(App\DTO\CurrencyResponse::class, $rate);
            $this->assertIsString($rate->pair);
            $this->assertIsFloat($rate->rate);
        }
    }
}