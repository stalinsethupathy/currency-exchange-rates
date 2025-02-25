<?php

require 'vendor/autoload.php';

use App\CurrencyFetcher;

$fetcher = new CurrencyFetcher();

// Default currency set to USD if no argument is passed
$baseCurrency = $argv[1] ?? 'USD';

try {
    $rates = $fetcher->getRates($baseCurrency);

    if (!empty($rates)) {
        echo "\nLive exchange rates for {$baseCurrency}:\n";
        echo "****************************\n";
        foreach ($rates as $pair => $rate) {
            echo "{$pair}: {$rate}\n";
        }
    } else {
        echo "No exchange rates found. Please check your API key and currency.\n";
    }
} catch (Exception $e) {
    // If an exception is thrown, it means there was an error fetching the rates
    echo "An error occurred: " . $e->getMessage() . "\n";
}
