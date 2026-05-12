<?php

namespace App\Tests\UI\Twig;

use App\Domain\Money\Model\ExchangeRate;
use App\Domain\Money\Service\ExchangeRateProvider;
use App\Infrastructure\Money\Repository\DoctrineExchangeRateRepository;
use App\UI\Twig\CurrencyExtension;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class CurrencyExtensionTest extends TestCase
{
    private function buildExtension(?ExchangeRate $cached, float $providerRate): CurrencyExtension
    {
        $rateRepo = $this->getMockBuilder(DoctrineExchangeRateRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findFresh', 'save'])
            ->getMock();
        $rateRepo->method('findFresh')->willReturn($cached);
        $rateRepo->method('save');

        $provider = $this->createMock(ExchangeRateProvider::class);
        $provider->method('getRate')->willReturn($providerRate);

        return new CurrencyExtension($provider, $rateRepo, new NullLogger());
    }

    public function testConvertCurrencySameCurrencyReturnsSameAmount(): void
    {
        $extension = $this->buildExtension(null, 1.0);

        $result = $extension->convertCurrency(100.0, 'EUR', 'EUR');

        $this->assertSame(100.0, $result);
    }

    public function testConvertCurrencyUsesCachedRate(): void
    {
        $cachedRate = new ExchangeRate('EUR', 'USD', 1.08, new \DateTime('today'));
        $extension = $this->buildExtension($cachedRate, 999.0);

        $result = $extension->convertCurrency(100.0, 'EUR', 'USD');

        $this->assertSame(108.0, $result);
    }

    public function testConvertCurrencyCallsProviderWhenNoCacheHit(): void
    {
        $provider = $this->createMock(ExchangeRateProvider::class);
        $provider->expects($this->once())->method('getRate')->willReturn(1.08);

        $rateRepo = $this->getMockBuilder(DoctrineExchangeRateRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findFresh', 'save'])
            ->getMock();
        $rateRepo->method('findFresh')->willReturn(null);
        $rateRepo->expects($this->once())->method('save');

        $extension = new CurrencyExtension($provider, $rateRepo, new NullLogger());

        $result = $extension->convertCurrency(50.0, 'EUR', 'USD');

        $this->assertSame(54.0, $result);
    }

    public function testConvertCurrencyReturnsFallbackAmountOnProviderException(): void
    {
        $provider = $this->createMock(ExchangeRateProvider::class);
        $provider->method('getRate')->willThrowException(new \RuntimeException('API down'));

        $rateRepo = $this->getMockBuilder(DoctrineExchangeRateRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findFresh', 'save'])
            ->getMock();
        $rateRepo->method('findFresh')->willReturn(null);

        $extension = new CurrencyExtension($provider, $rateRepo, new NullLogger());

        $result = $extension->convertCurrency(100.0, 'EUR', 'USD');

        $this->assertSame(100.0, $result);
    }

    public function testGetFiltersReturnsTwigFilter(): void
    {
        $extension = $this->buildExtension(null, 1.0);

        $filters = $extension->getFilters();

        $this->assertCount(1, $filters);
        $this->assertSame('convert_currency', $filters[0]->getName());
    }
}
