<?php

namespace App\Infrastructure\AIBundle\Provider;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ItineraryProvider
{
    public const BACKEND_OLLAMA = 'ollama';
    public const BACKEND_OPENAI = 'openai';

    private const TIMEOUT_SECONDS = 120;

    private const OLLAMA_DEFAULT_ENDPOINT = 'http://localhost:11434/api/chat';
    private const OPENAI_ENDPOINT = 'https://api.openai.com/v1/chat/completions';

    private const DEFAULT_OLLAMA_MODEL = 'llama3';
    private const DEFAULT_OPENAI_MODEL = 'gpt-4-turbo-preview';

    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
        private string $backend = self::BACKEND_OLLAMA,
        private string $openAiApiKey = '',
        private string $ollamaEndpoint = self::OLLAMA_DEFAULT_ENDPOINT,
        private string $ollamaModel = self::DEFAULT_OLLAMA_MODEL,
        private string $openAiModel = self::DEFAULT_OPENAI_MODEL,
    ) {
    }

    public static function createOllama(
        HttpClientInterface $httpClient,
        LoggerInterface $logger,
        string $endpoint = self::OLLAMA_DEFAULT_ENDPOINT,
        string $model = self::DEFAULT_OLLAMA_MODEL
    ): self {
        return new self(
            httpClient: $httpClient,
            logger: $logger,
            backend: self::BACKEND_OLLAMA,
            ollamaEndpoint: $endpoint,
            ollamaModel: $model
        );
    }

    public static function createOpenAI(
        HttpClientInterface $httpClient,
        LoggerInterface $logger,
        string $apiKey,
        string $model = self::DEFAULT_OPENAI_MODEL
    ): self {
        if (empty($apiKey)) {
            throw new \RuntimeException('OpenAI API key is required for OpenAI backend. Set OPENAI_API_KEY in .env');
        }
        return new self(
            httpClient: $httpClient,
            logger: $logger,
            backend: self::BACKEND_OPENAI,
            openAiApiKey: $apiKey,
            openAiModel: $model
        );
    }

    public function getBackend(): string
    {
        return $this->backend;
    }

    public function isOllama(): bool
    {
        return $this->backend === self::BACKEND_OLLAMA;
    }

    public function isOpenAI(): bool
    {
        return $this->backend === self::BACKEND_OPENAI;
    }

    public function getModel(): string
    {
        return $this->isOllama() ? $this->ollamaModel : $this->openAiModel;
    }

    public function generateItinerary(
        string $travelTitle,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
        array $locations,
        array $preferences = []
    ): array {
        $tripDays = $this->calculateTripDays($startDate, $endDate);
        $dates = $this->generateDateRange($startDate, $endDate);

        $locationsData = $this->prepareLocationsData($locations);
        $scheduledCount = count(array_filter($locationsData, fn($l) => $l['isScheduled']));
        $unscheduledCount = count($locationsData) - $scheduledCount;

        $systemPrompt = $this->buildSystemPrompt();
        $userPrompt = $this->buildUserPrompt(
            $travelTitle,
            $dates,
            $tripDays,
            $locationsData,
            $preferences
        );

        $this->logger->info(sprintf(
            '[ItineraryAI] Generating itinerary using %s (%s): %s, %d days, %d locations (%d unscheduled)',
            $this->backend,
            $this->getModel(),
            $travelTitle,
            $tripDays,
            count($locationsData),
            $unscheduledCount
        ));

        try {
            $result = $this->doRequest($systemPrompt, $userPrompt);

            $this->logger->info(sprintf(
                '[ItineraryAI] Itinerary generated successfully using %s',
                $this->backend
            ));

            return $result;

        } catch (\Exception $e) {
            $this->logger->error(sprintf('[ItineraryAI] Error: %s', $e->getMessage()));
            throw new \RuntimeException(
                sprintf('Itinerary generation failed with %s: %s', $this->backend, $e->getMessage()),
                0,
                $e
            );
        }
    }

    private function doRequest(string $systemPrompt, string $userPrompt): array
    {
        if ($this->isOllama()) {
            return $this->doOllamaRequest($systemPrompt, $userPrompt);
        }
        return $this->doOpenAIRequest($systemPrompt, $userPrompt);
    }

    private function doOllamaRequest(string $systemPrompt, string $userPrompt): array
    {
        $this->logger->info(sprintf(
            '[ItineraryAI] Calling Ollama at %s with model %s',
            $this->ollamaEndpoint,
            $this->ollamaModel
        ));

        $response = $this->httpClient->request('POST', $this->ollamaEndpoint, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => $this->ollamaModel,
                'stream' => false,
                'options' => [
                    'temperature' => 0.7,
                ],
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
            ],
            'timeout' => self::TIMEOUT_SECONDS,
        ]);

        $statusCode = $response->getStatusCode();

        if ($statusCode !== 200) {
            $content = $response->getContent(false);
            $this->logger->error(sprintf(
                '[ItineraryAI] Ollama returned HTTP %d: %s',
                $statusCode,
                $content
            ));
            throw new \RuntimeException(sprintf(
                'Ollama API error: HTTP %d. Is Ollama running? Try: ollama serve',
                $statusCode
            ));
        }

        $data = $response->toArray(false);
        $content = $data['message']['content'] ?? '{}';

        return $this->parseJsonResponse($content);
    }

    private function doOpenAIRequest(string $systemPrompt, string $userPrompt): array
    {
        if (empty($this->openAiApiKey)) {
            throw new \RuntimeException('OpenAI API key is not configured. Set OPENAI_API_KEY in .env');
        }

        $this->logger->info(sprintf(
            '[ItineraryAI] Calling OpenAI with model %s',
            $this->openAiModel
        ));

        $response = $this->httpClient->request('POST', self::OPENAI_ENDPOINT, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->openAiApiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => $this->openAiModel,
                'response_format' => ['type' => 'json_object'],
                'temperature' => 0.7,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
            ],
            'timeout' => self::TIMEOUT_SECONDS,
        ]);

        $statusCode = $response->getStatusCode();

        if ($statusCode !== 200) {
            $content = $response->getContent(false);
            $this->logger->error(sprintf(
                '[ItineraryAI] OpenAI returned HTTP %d: %s',
                $statusCode,
                $content
            ));
            throw new \RuntimeException('OpenAI API error: HTTP ' . $statusCode);
        }

        $data = $response->toArray(false);
        $content = $data['choices'][0]['message']['content'] ?? '{}';

        return $this->parseJsonResponse($content);
    }

    private function parseJsonResponse(string $content): array
    {
        $content = trim($content);

        if (str_starts_with($content, '```json')) {
            $content = substr($content, 7);
        }
        if (str_starts_with($content, '```')) {
            $content = substr($content, 3);
        }
        if (str_ends_with($content, '```')) {
            $content = substr($content, 0, -3);
        }

        $content = trim($content);

        $result = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->error(sprintf(
                '[ItineraryAI] JSON parse error: %s. Content: %s',
                json_last_error_msg(),
                substr($content, 0, 500)
            ));
            throw new \RuntimeException('AI returned invalid JSON. Try again or use a different model.');
        }

        if (!is_array($result)) {
            throw new \RuntimeException('AI returned unexpected response format');
        }

        return $result;
    }

    private function buildSystemPrompt(): string
    {
        return <<<'EOT'
You are an expert travel planner with decades of experience. Your goal is to optimize travel itineraries considering:

1. **GEOGRAPHIC PROXIMITY**: Locations close to each other should be visited on the same day to minimize travel time. Use the lat/lng coordinates to estimate proximity.

2. **TYPE OF ACTIVITY AND TIMING**:
   - Museums, cultural attractions: Best in the morning (10:00-13:00) when less crowded
   - Parks, beaches, outdoor activities: Best in the afternoon
   - Restaurants: Lunch (13:00-15:00) or dinner (20:00-22:00)
   - Hotels: Only one per city, usually check-in in the afternoon, check-out in the morning
   - Transportation (flights, trains): Mark the beginning or end of a day

3. **BALANCE**: Don't schedule more than 4-5 main activities per day. Leave free time between them.

4. **LOGICAL FLOW**:
   - If there's a flight/arrival: That day should be lighter
   - Group activities by geographic zone
   - Alternate intense activities with more relaxed ones

5. **ALREADY SCHEDULED LOCATIONS**: Respect them. Distribute unscheduled locations around them.

RESPOND ONLY in JSON format, no additional text.
EOT;
    }

    private function buildUserPrompt(
        string $travelTitle,
        array $dates,
        int $tripDays,
        array $locationsData,
        array $preferences
    ): string {
        $maxPerDay = $preferences['maxPerDay'] ?? 4;
        $noEarlyMornings = $preferences['noEarlyMornings'] ?? false;

        $datesList = implode("\n", array_map(
            fn($d, $i) => sprintf("  - Day %d: %s", $i + 1, $d),
            $dates,
            array_keys($dates)
        ));

        $locationsJson = json_encode($locationsData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $scheduledCount = count(array_filter($locationsData, fn($l) => $l['isScheduled']));
        $unscheduledCount = count($locationsData) - $scheduledCount;

        return <<<EOT
## TRIP: {$travelTitle}

### AVAILABLE DATES ({$tripDays} days):
{$datesList}

### USER PREFERENCES:
- Maximum {$maxPerDay} activities per day
- Avoid early mornings: " . ($noEarlyMornings ? 'Yes (start after 10:00)' : 'No preference') . "

### LOCATIONS (total: " . count($locationsData) . "):
- {$scheduledCount} already scheduled
- {$unscheduledCount} need to be assigned

Each location has:
- `id`: Unique identifier (IMPORTANT: include it in the output)
- `title`: Name
- `type`: Type (museum, hotel, restaurant, etc.)
- `lat`, `lng`: Coordinates for proximity
- `isScheduled`: true = already assigned to a day
- `scheduledDates`: dates when already scheduled

```json
{$locationsJson}
```

## YOUR TASK:

1. **RESPECT** already scheduled locations (`isScheduled: true`). Keep them on their days.

2. **OPTIMALLY DISTRIBUTE** unscheduled locations (`isScheduled: false`) across available days.

3. For EACH location, suggest:
   - `date`: date in YYYY-MM-DD format
   - `suggestedTime`: suggested time (e.g., "10:00-12:00")
   - `reason`: why you assigned it there (proximity to others, activity type, etc.)
   - `position`: order within the day (starting at 0)

## RESPONSE FORMAT (JSON):

```json
{
  "summary": "Brief summary of how you organized the itinerary",
  "totalDays": {$tripDays},
  "locationsScheduled": {$scheduledCount},
  "locationsAssigned": {$unscheduledCount},
  "days": [
    {
      "date": "2024-06-15",
      "dayNumber": 1,
      "title": "Descriptive title for the day",
      "locations": [
        {
          "id": "location-uuid",
          "title": "Location name",
          "suggestedTime": "10:00-12:00",
          "position": 0,
          "reason": "Close to hotel, museums are better in the morning",
          "wasAlreadyScheduled": false
        }
      ]
    }
  ],
  "unableToAssign": [],
  "warnings": [
    "Prado Museum needs advance booking",
    "2 days are quite full, consider redistributing"
  ]
}
```

IMPORTANT:
- The `id` field is CRITICAL. Use the exact same `id` from the input data.
- Include ALL locations, both scheduled and newly assigned.
- For already scheduled locations, set `wasAlreadyScheduled: true`.
- Respond ONLY with JSON, no other text.
EOT;
    }

    private function calculateTripDays(\DateTimeInterface $start, \DateTimeInterface $end): int
    {
        $interval = $start->diff($end);
        return $interval->days + 1;
    }

    private function generateDateRange(\DateTimeInterface $start, \DateTimeInterface $end): array
    {
        $dates = [];
        $current = clone $start;
        
        while ($current <= $end) {
            $dates[] = $current->format('Y-m-d');
            $current->modify('+1 day');
        }
        
        return $dates;
    }

    private function prepareLocationsData(array $locations): array
    {
        $result = [];

        foreach ($locations as $loc) {
            if (!is_object($loc)) {
                continue;
            }

            $id = null;
            $title = 'Unknown';
            $type = null;
            $lat = null;
            $lng = null;
            $isScheduled = false;
            $scheduledDates = [];

            if (method_exists($loc, 'getId')) {
                $idObj = $loc->getId();
                $id = is_object($idObj) && method_exists($idObj, '__toString') 
                    ? (string) $idObj 
                    : (string) $id;
            }

            if (method_exists($loc, 'getTitle')) {
                $title = $loc->getTitle() ?? 'Unknown';
            }

            if (method_exists($loc, 'getTypeLocation')) {
                $typeLoc = $loc->getTypeLocation();
                if ($typeLoc && method_exists($typeLoc, 'getTitle')) {
                    $type = $typeLoc->getTitle();
                }
            }

            if (method_exists($loc, 'getMark')) {
                $mark = $loc->getMark();
                if ($mark && method_exists($mark, 'getGeolocation')) {
                    $geo = $mark->getGeolocation();
                    if ($geo) {
                        $lat = method_exists($geo, 'lat') ? $geo->lat() : null;
                        $lng = method_exists($geo, 'lng') ? $geo->lng() : null;
                    }
                }
            }

            if (method_exists($loc, 'getVisitDates')) {
                $visitDates = $loc->getVisitDates();
                if ($visitDates && is_countable($visitDates) && count($visitDates) > 0) {
                    $isScheduled = true;
                    foreach ($visitDates as $vd) {
                        if ($vd && method_exists($vd, 'getVisitDate')) {
                            $date = $vd->getVisitDate();
                            if ($date instanceof \DateTimeInterface) {
                                $scheduledDates[] = $date->format('Y-m-d');
                            }
                        }
                    }
                }
            }

            $result[] = [
                'id' => $id,
                'title' => $title,
                'type' => $type ?? 'Other',
                'lat' => $lat,
                'lng' => $lng,
                'isScheduled' => $isScheduled,
                'scheduledDates' => $scheduledDates,
            ];
        }

        return $result;
    }
}
