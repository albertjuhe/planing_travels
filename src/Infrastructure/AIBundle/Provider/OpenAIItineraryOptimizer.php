<?php

namespace App\Infrastructure\AIBundle\Provider;

use App\Domain\Itinerary\Exceptions\ItineraryOptimizationFailed;
use App\Domain\Itinerary\Model\ItineraryLocationInput;
use App\Domain\Itinerary\Repository\ItineraryOptimizer;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface as HttpClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenAIItineraryOptimizer implements ItineraryOptimizer
{
    private const ENDPOINT = 'https://api.openai.com/v1/chat/completions';
    private const TIMEOUT_SECONDS = 30;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $apiKey,
        private readonly string $model,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @param ItineraryLocationInput[] $locations
     *
     * @return array<int, string[]>
     */
    public function optimize(
        array $locations,
        int $numberOfDays,
        ?\DateTime $startDate,
        string $destinationName,
        string $locale,
    ): array {
        if ($this->apiKey === '') {
            throw new ItineraryOptimizationFailed('OpenAI API key is not configured.');
        }

        if (empty($locations)) {
            return [];
        }

        $locationsJson = json_encode($this->buildLocationsPayload($locations), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $startDateStr = $startDate ? $startDate->format('Y-m-d (l)') : 'unknown';

        $userPrompt = sprintf(
            "Trip: \"%s\"\n"
            . "Start date: %s\n"
            . "Duration: %d day(s)\n"
            . "User locale: %s (toponyns may be in local language — preserve them exactly)\n\n"
            . "Locations to distribute:\n%s\n\n"
            . "Rules:\n"
            . "- Group locations geographically to minimise driving time between them.\n"
            . "- Assign all provided location_ids. Do NOT invent new ones.\n"
            . "- Distribute evenly; aim for at most 5 locations per day when possible.\n"
            . "- Every day from 1 to %d must appear in the plan.\n"
            . "- Return location_ids only — never titles.",
            $destinationName,
            $startDateStr,
            $numberOfDays,
            $locale,
            $locationsJson,
            $numberOfDays,
        );

        try {
            $response = $this->httpClient->request('POST', self::ENDPOINT, [
                'timeout' => self::TIMEOUT_SECONDS,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are an expert travel logistics planner. '
                                . 'Your job is to distribute a list of tourist locations across the available trip days, '
                                . 'grouping them geographically to minimise total driving time. '
                                . 'You MUST respond with valid JSON that strictly matches the provided schema. '
                                . 'Use only the location_ids supplied in the input — never invent new ones.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $userPrompt,
                        ],
                    ],
                    'response_format' => [
                        'type' => 'json_schema',
                        'json_schema' => [
                            'name' => 'itinerary_plan',
                            'strict' => true,
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'plan' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'day' => ['type' => 'integer'],
                                                'location_ids' => [
                                                    'type' => 'array',
                                                    'items' => ['type' => 'string'],
                                                ],
                                            ],
                                            'required' => ['day', 'location_ids'],
                                            'additionalProperties' => false,
                                        ],
                                    ],
                                ],
                                'required' => ['plan'],
                                'additionalProperties' => false,
                            ],
                        ],
                    ],
                ],
            ]);

            $statusCode = $response->getStatusCode();

            if ($statusCode !== 200) {
                $this->logger->warning('OpenAI itinerary optimizer returned HTTP {status}', [
                    'status' => $statusCode,
                    'body' => $response->getContent(false),
                ]);
                throw new ItineraryOptimizationFailed('OpenAI returned HTTP ' . $statusCode);
            }

            $data = $response->toArray(false);
        } catch (HttpClientExceptionInterface $e) {
            throw new ItineraryOptimizationFailed('OpenAI request failed: ' . $e->getMessage(), 0, $e);
        }

        $content = $data['choices'][0]['message']['content'] ?? null;

        if ($content === null) {
            throw new ItineraryOptimizationFailed('OpenAI response is missing message content.');
        }

        try {
            $parsed = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new ItineraryOptimizationFailed('OpenAI returned invalid JSON: ' . $e->getMessage(), 0, $e);
        }

        if (!isset($parsed['plan']) || !is_array($parsed['plan'])) {
            throw new ItineraryOptimizationFailed('OpenAI response is missing the "plan" array.');
        }

        return $this->validateAndNormalizePlan($parsed['plan'], $locations, $numberOfDays);
    }

    /**
     * @param ItineraryLocationInput[] $locations
     *
     * @return array<int, array<string, mixed>>
     */
    private function buildLocationsPayload(array $locations): array
    {
        $payload = [];
        foreach ($locations as $loc) {
            $item = [
                'location_id' => $loc->id,
                'title' => $loc->title,
            ];
            if ($loc->typeName !== null) {
                $item['type'] = $loc->typeName;
            }
            if ($loc->description !== null && $loc->description !== '') {
                $item['description'] = mb_substr($loc->description, 0, 120);
            }
            if ($loc->lat !== null && $loc->lng !== null) {
                $item['lat'] = $loc->lat;
                $item['lng'] = $loc->lng;
            }
            $payload[] = $item;
        }

        return $payload;
    }

    /**
     * Validates the AI-returned plan against the input constraints and returns a clean
     * map of day → location_id[].
     *
     * @param array<mixed>             $rawPlan
     * @param ItineraryLocationInput[] $inputLocations
     *
     * @return array<int, string[]>
     */
    private function validateAndNormalizePlan(array $rawPlan, array $inputLocations, int $numberOfDays): array
    {
        $validIds = [];
        foreach ($inputLocations as $loc) {
            $validIds[$loc->id] = true;
        }

        $plan = [];
        $seenIds = [];

        foreach ($rawPlan as $dayEntry) {
            if (!isset($dayEntry['day'], $dayEntry['location_ids'])) {
                continue;
            }

            $day = (int) $dayEntry['day'];

            if ($day < 1 || $day > $numberOfDays) {
                $this->logger->warning('OpenAI itinerary: skipping out-of-range day {day} (trip is {max} days)', [
                    'day' => $day,
                    'max' => $numberOfDays,
                ]);
                continue;
            }

            $ids = [];
            foreach ((array) $dayEntry['location_ids'] as $id) {
                $id = (string) $id;
                if (!isset($validIds[$id])) {
                    $this->logger->warning('OpenAI itinerary: unknown location_id "{id}" — skipped', ['id' => $id]);
                    continue;
                }
                if (isset($seenIds[$id])) {
                    $this->logger->warning('OpenAI itinerary: duplicate location_id "{id}" — skipped', ['id' => $id]);
                    continue;
                }
                $seenIds[$id] = true;
                $ids[] = $id;
            }

            if (!empty($ids)) {
                $plan[$day] = $ids;
            }
        }

        return $plan;
    }
}
