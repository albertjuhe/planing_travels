<?php

namespace App\UI\Twig;

use App\Domain\Money\Model\ExchangeRate;
use App\Domain\Money\Service\ExchangeRateProvider;
use App\Infrastructure\Money\Repository\DoctrineExchangeRateRepository;
use Psr\Log\LoggerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class CurrencyExtension extends AbstractExtension
{
    private ExchangeRateProvider $provider;
    private DoctrineExchangeRateRepository $rateRepo;
    private LoggerInterface $logger;

    public function __construct(
        ExchangeRateProvider $provider,
        DoctrineExchangeRateRepository $rateRepo,
        LoggerInterface $logger
    ) {
        $this->provider = $provider;
        $this->rateRepo = $rateRepo;
        $this->logger = $logger;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('convert_currency', [$this, 'convertCurrency']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('exchange_rate', [$this, 'getExchangeRate']),
        ];
    }

    public function convertCurrency(float $amount, string $from, string $to, ?\DateTime $date = null): float
    {
        if (strtoupper($from) === strtoupper($to)) {
            return $amount;
        }

        try {
            $rate = $this->getExchangeRate($from, $to, $date);

            return round($amount * $rate, 2);
        } catch (\Throwable $e) {
            $this->logger->warning('CurrencyExtension: could not convert', ['error' => $e->getMessage()]);

            return $amount;
        }
    }

    public function getExchangeRate(string $from, string $to, ?\DateTime $date = null): float
    {
        $date = $date ?? new \DateTime('today');

        $cached = $this->rateRepo->findFresh($from, $to, $date);
        if ($cached !== null) {
            return $cached->getRate();
        }

        $rate = $this->provider->getRate($from, $to, $date);
        $exchangeRate = new ExchangeRate($from, $to, $rate, $date);
        $this->rateRepo->save($exchangeRate);

        return $rate;
    }
}
