<?php

namespace App\Infrastructure\AIBundle\Provider;

use App\Domain\Itinerary\Exceptions\LocationSuggestionFailed;
use App\Domain\Itinerary\Model\SuggestedLocation;
use App\Domain\Itinerary\Repository\LocationSuggester;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface as HttpClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenAILocationSuggester implements LocationSuggester
{
    private const ENDPOINT        = 'https://api.openai.com/v1/chat/completions';
    private const TIMEOUT_SECONDS = 60;

    // Map of common type keywords to the TypeLocation titles used in the app.
    private const TYPE_MAP = [
        'hotel'       => 'Hotel',
        'house'       => 'House',
        'hostel'      => 'Hotel',
        'apartment'   => 'Hotel',
        'museum'      => 'Museum',
        'beach'       => 'Beach',
        'park'        => 'Park',
        'restaurant'  => 'Restaurant',
        'lunch'       => 'Lunch',
        'coffee'      => 'Coffee',
        'airport'     => 'Airport',
        'monument'    => 'Monument',
        'viewpoint'   => 'Viewpoint',
        'city'        => 'City',
        'town'        => 'City',
        'village'     => 'City',
        'bar'         => 'Bar',
        'shop'        => 'Shop',
        'camping'     => 'Camping',
        'hospital'    => 'Hospital',
        'cinema'      => 'Cinema',
        'train'       => 'Train',
        'bus'         => 'Bus',
    ];

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string             $apiKey,
        private readonly string             $model,
        private readonly LoggerInterface    $logger,
    ) {
    }

    /**
     * @return SuggestedLocation[]
     */
    public function suggest(
        string $destination,
        int    $numberOfDays,
        string $locale,
        string $preferences = '',
        array  $existing = [],
    ): array {
        if ($this->apiKey === '') {
            throw new LocationSuggestionFailed('OpenAI API key is not configured.');
        }

        $existingBlock = '';
        if (!empty($existing)) {
            $existingBlock = "Already added (do NOT suggest these again):\n"
                . implode("\n", array_map(static fn ($e) => "  - {$e}", $existing))
                . "\n\n";
        }

        $preferencesBlock = '';
        if (trim($preferences) !== '') {
            $preferencesBlock = "User preferences:\n" . mb_substr(trim($preferences), 0, 500) . "\n\n";
        }

        // Target: ~4 locations per day, capped to avoid slow responses.
        $targetCount = min(max($numberOfDays * 4, 8), 25);

        $userPrompt = sprintf(
            "Suggest the best locations to visit for this trip:\n"
            . "- Destination: %s\n"
            . "- Duration: %d days\n"
            . "- User locale: %s\n\n"
            . "%s%s"
            . "Return exactly %d location suggestions.\n"
            . "For each location provide:\n"
            . "  - name: the well-known place name (in the local language if applicable)\n"
            . "  - description: 1-2 sentences explaining why it is worth visiting\n"
            . "  - type: one of: hotel, house, museum, beach, park, restaurant, coffee, monument, viewpoint, city, bar, shop, camping, airport, train, bus, cinema, hospital\n"
            . "  - lat: latitude as decimal number\n"
            . "  - lng: longitude as decimal number\n"
            . "  - address: full address or area (e.g. \"Via Roma 1, Florence, Italy\")\n\n"
            . "Mix types logically: include accommodation(s), meals, and a variety of tourist attractions.\n"
            . "Order the suggestions geographically (cluster nearby places together).\n"
            . "Use real, accurate coordinates. NEVER invent places.",
            $destination,
            $numberOfDays,
            $locale,
            $existingBlock,
            $preferencesBlock,
            $targetCount,
        );

        $systemPrompt = 'You are an expert travel guide with deep knowledge of world destinations. '
            . 'You suggest real, accurate, interesting places to visit for a given trip. '
            . 'You always provide real GPS coordinates and full addresses. '
            . 'You output JSON matching the provided schema exactly. No prose outside the JSON.';

        try {
            $response = $this->httpClient->request('POST', self::ENDPOINT, [
                'timeout' => self::TIMEOUT_SECONDS,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'model'       => $this->model,
                    'temperature' => 0.7,
                    'messages'    => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user',   'content' => $userPrompt],
                    ],
                    'response_format' => [
                        'type'        => 'json_schema',
                        'json_schema' => [
                            'name'   => 'location_suggestions',
                            'strict' => true,
                            'schema' => [
                                'type'       => 'object',
                                'properties' => [
                                    'suggestions' => [
                                        'type'  => 'array',
                                        'items' => [
                                            'type'       => 'object',
                                            'properties' => [
                                                'name'        => ['type' => 'string'],
                                                'description' => ['type' => 'string'],
                                                'type'        => ['type' => 'string'],
                                                'lat'         => ['type' => 'number'],
                                                'lng'         => ['type' => 'number'],
                                                'address'     => ['type' => 'string'],
                                            ],
                                            'required'             => ['name', 'description', 'type', 'lat', 'lng', 'address'],
                                            'additionalProperties' => false,
                                        ],
                                    ],
                                ],
                                'required'             => ['suggestions'],
                                'additionalProperties' => false,
                            ],
                        ],
                    ],
                ],
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                $body = mb_substr((string) $response->getContent(false), 0, 400);
                $this->logger->warning('OpenAI location suggester HTTP {status}', ['status' => $statusCode, 'body' => $body]);
                throw new LocationSuggestionFailed("OpenAI returned HTTP {$statusCode}. {$body}");
            }

            $data = $response->toArray(false);
        } catch (HttpClientExceptionInterface $e) {
            throw new LocationSuggestionFailed('OpenAI request failed: ' . $e->getMessage(), 0, $e);
        }

        $content = $data['choices'][0]['message']['content'] ?? null;
        if ($content === null) {
            throw new LocationSuggestionFailed('OpenAI response is missing message content.');
        }

        try {
            $parsed = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new LocationSuggestionFailed('OpenAI returned invalid JSON: ' . $e->getMessage(), 0, $e);
        }

        if (!isset($parsed['suggestions']) || !is_array($parsed['suggestions'])) {
            throw new LocationSuggestionFailed('OpenAI response is missing the "suggestions" array.');
        }

        return $this->parseSuggestions($parsed['suggestions']);
    }

    /**
     * @param array<mixed> $raw
     *
     * @return SuggestedLocation[]
     */
    private function parseSuggestions(array $raw): array
    {
        $result = [];
        foreach ($raw as $item) {
            if (!isset($item['name'], $item['type'])) {
                continue;
            }

            $lat = isset($item['lat']) && is_numeric($item['lat']) ? (float) $item['lat'] : null;
            $lng = isset($item['lng']) && is_numeric($item['lng']) ? (float) $item['lng'] : null;

            $result[] = new SuggestedLocation(
                name: (string) $item['name'],
                description: (string) ($item['description'] ?? ''),
                typeName: $this->normalizeType((string) $item['type']),
                lat: $lat,
                lng: $lng,
                address: isset($item['address']) ? (string) $item['address'] : null,
            );
        }

        return $result;
    }

    private function normalizeType(string $raw): string
    {
        $lower = strtolower(trim($raw));
        return self::TYPE_MAP[$lower] ?? 'Monument';
    }
}
