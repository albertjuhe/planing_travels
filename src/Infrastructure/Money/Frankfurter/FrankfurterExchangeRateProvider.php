<?php

namespace App\Infrastructure\Money\Frankfurter;

use App\Domain\Money\Model\ExchangeRate;
use App\Domain\Money\Service\ExchangeRateProvider;
use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;

class FrankfurterExchangeRateProvider implements ExchangeRateProvider
{
    private ClientInterface $client;
    private LoggerInterface $logger;
    private string $baseUrl;

    public function __construct(
        ClientInterface $client,
        LoggerInterface $logger,
        string $baseUrl = 'https://api.frankfurter.app'
    ) {
        $this->client = $client;
        $this->logger = $logger;
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function getRate(string $from, string $to, ?\DateTime $date = null): float
    {
        $from = strtoupper($from);
        $to = strtoupper($to);

        if ($from === $to) {
            return 1.0;
        }

        $dateStr = $date ? $date->format('Y-m-d') : 'latest';
        $url = "{$this->baseUrl}/{$dateStr}?from={$from}&to={$to}";

        try {
            $response = $this->client->request('GET', $url, ['timeout' => 5]);
            $data = json_decode((string) $response->getBody(), true);

            if (!isset($data['rates'][$to])) {
                throw new \RuntimeException("Rate {$from}/{$to} not found in Frankfurter response.");
            }

            return (float) $data['rates'][$to];
        } catch (\Throwable $e) {
            $this->logger->warning('FrankfurterExchangeRateProvider failed', [
                'from' => $from, 'to' => $to, 'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException("Could not fetch exchange rate {$from}/{$to}: " . $e->getMessage(), 0, $e);
        }
    }
}
