<?php
declare(strict_types=1);
namespace App;

use App\DTO\CurrencyRequest;
use App\DTO\CurrencyResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use Dotenv\Dotenv;
use Exception;

readonly class CurrencyFetcher
{
    private Client $client;
    private string $apiKey, $baseUri;

    public function __construct(?Client $client = null)
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
        $this->apiKey  = $_ENV['API_KEY'] ?? '';
        $this->baseUri = $_ENV['BASE_URI'] ?? '';

        if (empty($this->apiKey)) {
            throw new Exception('API_KEY is empty, please set in the .env file.');
        }
        if (empty($this->baseUri)) {
            throw new Exception('BASE_URI is empty, please set in the .env file.');
        }
        $this->client = $client ?? new Client(['base_uri' => $this->baseUri]);
    }

    public function getRates(CurrencyRequest $request): array
    {
        $queryParams = [
            'access_key' => $this->apiKey,
            'source' => $request->baseCurrency
        ];
        $url = "live?" . http_build_query($queryParams);
        try {
            $response   = $this->client->get($url, ['query' => $queryParams]);
            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                throw new Exception("Error: Received status code {$statusCode} from API.");
            }
            $body   = $response->getBody()->getContents();
            $data   = json_decode($body, true);
            $quotes = $data['quotes'] ?? [];
            return array_map(fn($pair, $rate) => new CurrencyResponse($pair, (float) $rate), array_keys($quotes), $quotes);
        } catch (ConnectException $e) {
            throw new Exception("Network error: Unable to connect to the API");
        } catch (RequestException $e) {
            throw new Exception("HTTP error: " . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception("An error occurred: " . $e->getMessage());
        }
    }

    public function convertCurrency(string $fromCurrency, string $toCurrency, float $amount): float
    {
        $request = new CurrencyRequest($fromCurrency);
        $rates = $this->getRates($request);
        $fromRate = array_filter($rates, fn($rate) => $rate->pair === "{$fromCurrency}{$toCurrency}");
        if (empty($fromRate)) {
            throw new Exception("Conversion rate from {$fromCurrency} to {$toCurrency} not found.");
        }
        return $amount * array_values($fromRate)[0]->rate;
    }
}