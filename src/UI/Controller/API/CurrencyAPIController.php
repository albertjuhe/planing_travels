<?php

namespace App\UI\Controller\API;

use App\Infrastructure\Money\Frankfurter\FrankfurterExchangeRateProvider;
use App\Infrastructure\Money\Repository\DoctrineExchangeRateRepository;
use App\Domain\Money\Model\ExchangeRate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class CurrencyAPIController extends AbstractController
{
    private FrankfurterExchangeRateProvider $provider;
    private DoctrineExchangeRateRepository $rateRepo;

    public function __construct(
        FrankfurterExchangeRateProvider $provider,
        DoctrineExchangeRateRepository $rateRepo
    ) {
        $this->provider = $provider;
        $this->rateRepo = $rateRepo;
    }

    #[Route('/api/currency/convert', name: 'api_currency_convert', methods: ['GET'])]
    public function convert(Request $request): JsonResponse
    {
        $amount = (float) ($request->query->get('amount', 1));
        $from = strtoupper(trim($request->query->get('from', 'EUR')));
        $to = strtoupper(trim($request->query->get('to', 'USD')));
        $dateStr = $request->query->get('date');

        $date = null;
        if ($dateStr) {
            $date = \DateTime::createFromFormat('Y-m-d', $dateStr) ?: null;
        }
        $date = $date ?? new \DateTime('today');

        if ($from === $to) {
            return new JsonResponse(['from' => $from, 'to' => $to, 'rate' => 1.0, 'result' => $amount]);
        }

        try {
            $cached = $this->rateRepo->findFresh($from, $to, $date);
            $rate = $cached ? $cached->getRate() : $this->provider->getRate($from, $to, $date);

            if (!$cached) {
                $this->rateRepo->save(new ExchangeRate($from, $to, $rate, $date));
            }

            return new JsonResponse([
                'from' => $from,
                'to' => $to,
                'rate' => $rate,
                'result' => round($amount * $rate, 2),
                'date' => $date->format('Y-m-d'),
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => 'Could not fetch exchange rate. Please try again later.'], 503);
        }
    }
}
