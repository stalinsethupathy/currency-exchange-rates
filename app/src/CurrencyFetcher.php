<?php

declare(strict_types=1);

namespace App;

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

        $this->apiKey = $_ENV['API_KEY'] ?? '';
        $this->baseUri = $_ENV['BASE_URI'] ?? '';

        // Validate API Key
        if (empty($this->apiKey)) {
            throw new Exception('API_KEY is not set in the .env file.');
        }

        // Validate Base URI
        if (empty($this->baseUri)) {
            throw new Exception('BASE_URI is not set in the .env file.');
        }

        // mock client for testing
        $this->client = $client ?? new Client(['base_uri' => $this->baseUri]);
    }

    public function getRates(string $baseCurrency): array
    {
        $queryParams = [
            'access_key' => $this->apiKey,
            'source' => $baseCurrency
        ];

        $url = "live?" . http_build_query($queryParams);

        try {
            $response = $this->client->get($url, ['query' => $queryParams]);
            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                throw new Exception("Error: Received status code {$statusCode} from API.");
            }

            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);

            return $data['quotes'] ?? [];
        } catch (ConnectException $e) {
            throw new Exception("Network error: Unable to connect to the API");
        } catch (RequestException $e) {
            throw new Exception("HTTP error: " . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception("An error occurred: " . $e->getMessage());
        }
    }
}


