<?php

namespace App\Tests\Infrastructure\Weather\OpenMeteo;

use App\Domain\Weather\Model\WeatherForecast;
use App\Infrastructure\Weather\OpenMeteo\OpenMeteoWeatherProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class OpenMeteoWeatherProviderTest extends TestCase
{
    private function buildProvider(array $responses): OpenMeteoWeatherProvider
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        return new OpenMeteoWeatherProvider($client, new NullLogger(), 'https://api.open-meteo.com/v1');
    }

    private function makeForecastBody(float $tempMin, float $tempMax, int $code): string
    {
        return json_encode([
            'latitude' => 40.42,
            'longitude' => -3.7,
            'daily' => [
                'time' => ['2024-06-15'],
                'temperature_2m_min' => [$tempMin],
                'temperature_2m_max' => [$tempMax],
                'weathercode' => [$code],
            ],
        ]);
    }

    public function testGetForecastForFutureDateReturnsForecast(): void
    {
        $body = $this->makeForecastBody(15.5, 28.3, 1);
        $provider = $this->buildProvider([new Response(200, [], $body)]);

        $date = new \DateTime('tomorrow');
        $forecast = $provider->getForecast(40.4168, -3.7038, $date);

        $this->assertInstanceOf(WeatherForecast::class, $forecast);
        $this->assertSame(15.5, $forecast->getTempMin());
        $this->assertSame(28.3, $forecast->getTempMax());
        $this->assertSame(1, $forecast->getWeatherCode());
        $this->assertFalse($forecast->isHistorical());
    }

    public function testGetForecastForPastDateReturnsHistorical(): void
    {
        $body = $this->makeForecastBody(10.0, 22.0, 3);
        $provider = $this->buildProvider([new Response(200, [], $body)]);

        $date = new \DateTime('2020-06-15');
        $forecast = $provider->getForecast(48.8566, 2.3522, $date);

        $this->assertTrue($forecast->isHistorical());
        $this->assertSame(10.0, $forecast->getTempMin());
    }

    public function testGetForecastParsesIconAndDescription(): void
    {
        $body = $this->makeForecastBody(5.0, 15.0, 0);
        $provider = $this->buildProvider([new Response(200, [], $body)]);

        $forecast = $provider->getForecast(0.0, 0.0, new \DateTime('tomorrow'));

        $this->assertSame('☀️', $forecast->getIcon());
        $this->assertSame('Clear sky', $forecast->getDescription());
    }

    public function testHttpErrorThrowsRuntimeException(): void
    {
        $this->expectException(\RuntimeException::class);

        $provider = $this->buildProvider([new Response(500, [], 'Error')]);

        $provider->getForecast(0.0, 0.0, new \DateTime('tomorrow'));
    }

    public function testEmptyDailyDataHandledGracefully(): void
    {
        $body = json_encode(['daily' => []]);
        $provider = $this->buildProvider([new Response(200, [], $body)]);

        $forecast = $provider->getForecast(0.0, 0.0, new \DateTime('tomorrow'));

        $this->assertNull($forecast->getTempMin());
        $this->assertNull($forecast->getTempMax());
        $this->assertNull($forecast->getWeatherCode());
    }
}
