<?php

namespace App\Tests\Infrastructure\Money\Frankfurter;

use App\Infrastructure\Money\Frankfurter\FrankfurterExchangeRateProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class FrankfurterExchangeRateProviderTest extends TestCase
{
    private function buildProvider(array $responses): FrankfurterExchangeRateProvider
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        return new FrankfurterExchangeRateProvider($client, new NullLogger(), 'https://api.frankfurter.app');
    }

    public function testGetRateReturnsCorrectValueFromResponse(): void
    {
        $body = json_encode(['amount' => 1, 'base' => 'EUR', 'date' => '2024-06-15', 'rates' => ['USD' => 1.08]]);
        $provider = $this->buildProvider([new Response(200, [], $body)]);

        $rate = $provider->getRate('EUR', 'USD');

        $this->assertSame(1.08, $rate);
    }

    public function testSameCurrencyReturnOneWithoutApiCall(): void
    {
        $provider = $this->buildProvider([]);

        $rate = $provider->getRate('EUR', 'EUR');

        $this->assertSame(1.0, $rate);
    }

    public function testSameCurrencyLowercaseReturnOne(): void
    {
        $provider = $this->buildProvider([]);

        $rate = $provider->getRate('eur', 'EUR');

        $this->assertSame(1.0, $rate);
    }

    public function testGetRateWithDateUsesDateInUrl(): void
    {
        $body = json_encode(['amount' => 1, 'base' => 'GBP', 'date' => '2024-01-15', 'rates' => ['JPY' => 185.5]]);
        $provider = $this->buildProvider([new Response(200, [], $body)]);

        $rate = $provider->getRate('GBP', 'JPY', new \DateTime('2024-01-15'));

        $this->assertSame(185.5, $rate);
    }

    public function testHttpErrorThrowsRuntimeException(): void
    {
        $this->expectException(\RuntimeException::class);

        $provider = $this->buildProvider([new Response(500, [], 'Internal Server Error')]);

        $provider->getRate('EUR', 'USD');
    }

    public function testMissingRateInResponseThrowsRuntimeException(): void
    {
        $this->expectException(\RuntimeException::class);

        $body = json_encode(['amount' => 1, 'base' => 'EUR', 'rates' => []]);
        $provider = $this->buildProvider([new Response(200, [], $body)]);

        $provider->getRate('EUR', 'USD');
    }
}
