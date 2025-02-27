<?php
require 'vendor/autoload.php';
use App\CurrencyFetcher;
use App\DTO\CurrencyRequest;

$fetcher = new CurrencyFetcher();

// Default currency set to USD if no argument is passed
$baseCurrency = $argv[1] ?? 'USD';

$request = new CurrencyRequest($baseCurrency);

try {
    $rates = $fetcher->getRates($request);

    if (!empty($rates)) {
        echo "\nLive exchange rates for {$baseCurrency}:\n";
        echo "****************************\n";
        foreach ($rates as $rate) {
            echo "{$rate->pair}: {$rate->rate}\n";
        }
    } else {
        echo "No exchange rates found. Please check your API key and currency.\n";
    }
} catch (Exception $e) {
    // If an exception is thrown, it means there was an error fetching the rates
    echo "An error occurred: " . $e->getMessage() . "\n";
}