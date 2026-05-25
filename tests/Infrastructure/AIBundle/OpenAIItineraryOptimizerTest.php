<?php

namespace App\Tests\Infrastructure\AIBundle;

use App\Domain\Itinerary\Exceptions\ItineraryOptimizationFailed;
use App\Domain\Itinerary\Model\ItineraryLocationInput;
use App\Infrastructure\AIBundle\Provider\OpenAIItineraryOptimizer;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class OpenAIItineraryOptimizerTest extends TestCase
{
    private function makeOptimizer(array|string $responses, string $apiKey = 'test-key', string $model = 'gpt-4o-mini'): OpenAIItineraryOptimizer
    {
        if (is_array($responses)) {
            $mockResponses = array_map(
                static fn ($r) => is_string($r) ? new MockResponse($r) : $r,
                $responses
            );
            $httpClient = new MockHttpClient($mockResponses);
        } else {
            $httpClient = new MockHttpClient(new MockResponse($responses));
        }

        return new OpenAIItineraryOptimizer($httpClient, $apiKey, $model, new NullLogger());
    }

    private function locations(): array
    {
        return [
            new ItineraryLocationInput('id-1', 'Tirana', 'Capital city', 'City', 41.33, 19.82),
            new ItineraryLocationInput('id-2', 'Berat', 'City of a thousand windows', 'City', 40.71, 19.95),
            new ItineraryLocationInput('id-3', 'Ksamil', 'Beautiful beach', 'Beach', 39.77, 20.01),
        ];
    }

    private function validApiResponse(array $plan): string
    {
        return json_encode([
            'choices' => [[
                'message' => [
                    'content' => json_encode(['plan' => $plan]),
                ],
            ]],
        ]);
    }

    public function testEmptyApiKeyThrowsBeforeRequest(): void
    {
        $optimizer = $this->makeOptimizer([], '');
        $this->expectException(ItineraryOptimizationFailed::class);
        $optimizer->optimize($this->locations(), 3, null, 'Albania', 'en');
    }

    public function testValidResponseReturnsPlan(): void
    {
        $rawPlan = [
            ['day' => 1, 'location_ids' => ['id-1']],
            ['day' => 2, 'location_ids' => ['id-2', 'id-3']],
        ];
        $optimizer = $this->makeOptimizer($this->validApiResponse($rawPlan));

        $result = $optimizer->optimize($this->locations(), 3, new \DateTime('2026-06-01'), 'Albania', 'en');

        $this->assertArrayHasKey(1, $result);
        $this->assertArrayHasKey(2, $result);
        $this->assertSame(['id-1'], $result[1]);
        $this->assertSame(['id-2', 'id-3'], $result[2]);
    }

    public function testHttp401ThrowsOptimizationFailed(): void
    {
        $mockResponse = new MockResponse('{"error":{"message":"Invalid API key"}}', ['http_code' => 401]);
        $optimizer = $this->makeOptimizer([$mockResponse]);

        $this->expectException(ItineraryOptimizationFailed::class);
        $optimizer->optimize($this->locations(), 2, null, 'Albania', 'en');
    }

    public function testHttp429ThrowsOptimizationFailed(): void
    {
        $mockResponse = new MockResponse('{"error":{"message":"Rate limit exceeded"}}', ['http_code' => 429]);
        $optimizer = $this->makeOptimizer([$mockResponse]);

        $this->expectException(ItineraryOptimizationFailed::class);
        $optimizer->optimize($this->locations(), 2, null, 'Albania', 'en');
    }

    public function testInvalidJsonThrowsOptimizationFailed(): void
    {
        $brokenJson = '{"choices":[{"message":{"content":"this is not json"}}]}';
        $optimizer = $this->makeOptimizer($brokenJson);

        $this->expectException(ItineraryOptimizationFailed::class);
        $optimizer->optimize($this->locations(), 2, null, 'Albania', 'en');
    }

    public function testMissingPlanKeyThrowsOptimizationFailed(): void
    {
        $badStructure = json_encode([
            'choices' => [[
                'message' => ['content' => json_encode(['wrong_key' => []])],
            ]],
        ]);
        $optimizer = $this->makeOptimizer($badStructure);

        $this->expectException(ItineraryOptimizationFailed::class);
        $optimizer->optimize($this->locations(), 2, null, 'Albania', 'en');
    }

    public function testUnknownIdsAreFilteredOut(): void
    {
        $rawPlan = [
            ['day' => 1, 'location_ids' => ['id-1', 'id-invented-by-ai']],
        ];
        $optimizer = $this->makeOptimizer($this->validApiResponse($rawPlan));

        $result = $optimizer->optimize($this->locations(), 2, null, 'Albania', 'en');

        $this->assertSame(['id-1'], $result[1]);
    }

    public function testDuplicateIdsAreDeduped(): void
    {
        $rawPlan = [
            ['day' => 1, 'location_ids' => ['id-1', 'id-1', 'id-2']],
        ];
        $optimizer = $this->makeOptimizer($this->validApiResponse($rawPlan));

        $result = $optimizer->optimize($this->locations(), 2, null, 'Albania', 'en');

        $this->assertSame(['id-1', 'id-2'], $result[1]);
    }

    public function testOutOfRangeDaysAreSkipped(): void
    {
        $rawPlan = [
            ['day' => 1, 'location_ids' => ['id-1']],
            ['day' => 99, 'location_ids' => ['id-2']],
        ];
        $optimizer = $this->makeOptimizer($this->validApiResponse($rawPlan));

        $result = $optimizer->optimize($this->locations(), 3, null, 'Albania', 'en');

        $this->assertArrayHasKey(1, $result);
        $this->assertArrayNotHasKey(99, $result);
    }

    public function testEmptyLocationsReturnsEmptyPlan(): void
    {
        $optimizer = $this->makeOptimizer('');
        $result = $optimizer->optimize([], 3, null, 'Albania', 'en');
        $this->assertSame([], $result);
    }
}
