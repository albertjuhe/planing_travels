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
        string $additionalNotes = '',
    ): array {
        if ($this->apiKey === '') {
            throw new ItineraryOptimizationFailed('OpenAI API key is not configured.');
        }

        if (empty($locations)) {
            return [];
        }

        $payload = $this->buildLocationsPayload($locations);
        $locationsJson = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $lodgingIds    = [];
        $lodgingCount  = 0;
        $lodgingTitles = [];
        foreach ($locations as $loc) {
            if ($loc->isLodging) {
                $lodgingIds[$loc->id] = true;
                ++$lodgingCount;
                $lodgingTitles[] = $loc->title;
            }
        }

        $this->logger->info('OpenAI itinerary: planning {total} location(s) ({lodgings} lodging(s)) over {days} day(s)', [
            'total' => count($locations),
            'lodgings' => $lodgingCount,
            'lodging_titles' => $lodgingTitles,
            'days' => $numberOfDays,
            'destination' => $destinationName,
        ]);

        $startDateStr = $startDate ? $startDate->format('Y-m-d (l)') : 'unknown';

        $additionalNotesBlock = '';
        $trimmedNotes = trim($additionalNotes);
        if ($trimmedNotes !== '') {
            // Cap user-supplied notes to keep the prompt bounded.
            $trimmedNotes = mb_substr($trimmedNotes, 0, 1000);
            $additionalNotesBlock = "User preferences (apply these IN ADDITION to the routing rules below; never invent location_ids):\n"
                . $trimmedNotes . "\n\n";
        }

        $userPrompt = sprintf(
            "Trip: \"%s\"\n"
            . "Start date: %s\n"
            . "Duration: %d day(s)\n"
            . "User locale: %s (toponyms may be in local language — preserve them exactly)\n"
            . "Total locations: %d (lodgings: %d, activities: %d)\n\n"
            . "%s"
            . "Locations to distribute:\n%s\n\n"
            . "=== HARD RULES (violating any of these makes the answer invalid) ===\n"
            . "R1. EVERY single location_id from the input MUST appear in the plan. Zero locations may be left out.\n"
            . "R2. ACTIVITIES (locations with \"is_lodging\": false) MUST appear EXACTLY ONCE across the whole plan.\n"
            . "R3. LODGINGS (locations with \"is_lodging\": true) MUST be REPEATED on every night the traveller sleeps there.\n"
            . "     - If the traveller sleeps 3 nights in the same lodging, that lodging_id appears on 3 separate days.\n"
            . "     - Accommodations REPEAT across consecutive days UNTIL the itinerary moves to a new region.\n"
            . "     - It is REQUIRED, not optional, to repeat the lodging across consecutive days when the activities of\n"
            . "       those days are in the same area.\n"
            . "     - The same lodging_id MUST NOT appear twice in the SAME day (it is the same physical bed).\n"
            . "R4. Every day from 1 to %d MUST be present in the plan and MUST contain at least one location_id\n"
            . "     (typically: the lodging plus the activities of that day).\n"
            . "R5. Use only the location_ids supplied above. NEVER invent new ids or titles.\n\n"
            . "=== ROUTING STRATEGY (apply these to decide WHICH location goes on WHICH day) ===\n"
            . "S1. LODGINGS ARE BASE CAMPS: each lodging is the cuartel general for the day(s) it is assigned to.\n"
            . "     The traveller checks in in the evening, sleeps, and departs in the morning to visit the day's activities,\n"
            . "     then returns to the SAME lodging for the night unless transitioning to a new region.\n"
            . "S2. Cluster activities geographically around their day's lodging to minimise driving time.\n"
            . "     NEVER make the traveller drive 3 hours from north to south and back to the north on the same day.\n"
            . "S3. Optimise for total driving time across the whole trip:\n"
            . "     - Group nearby activities into the same day.\n"
            . "     - When a day's lodging is far from the next day's lodging, choose activities that lie on the\n"
            . "       road BETWEEN them, so the transition day is productive (no empty driving days).\n"
            . "S4. If the number of activities is greater than the number of days, balance them so each day has a\n"
            . "     reasonable workload (target ~2 to 5 activities per day, never zero).\n"
            . "S5. If the number of activities is smaller than the number of days, distribute them across days but\n"
            . "     ensure every day still has the appropriate lodging entry (rule R4).\n"
            . "S6. ORDER WITHIN A DAY: place the lodging FIRST in location_ids (base camp), then the activities ordered\n"
            . "     by an efficient geographic route starting from the lodging\n"
            . "     (e.g. Hotel → Point A → Point B → Point C, with implicit return to Hotel at night).\n"
            . "S7. Aim to make the most of each day — don't leave half-empty days while another day is overloaded.\n\n"
            . "Return JSON only, matching the provided schema strictly.",
            $destinationName,
            $startDateStr,
            $numberOfDays,
            $locale,
            count($payload),
            $lodgingCount,
            count($payload) - $lodgingCount,
            $additionalNotesBlock,
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
                    'temperature' => 0.2,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are an expert travel logistics engine and itinerary optimizer. '
                                . 'Your task is to organize a list of unstructured locations into a logical, highly efficient daily travel itinerary. '
                                . "\n\n"
                                . 'You must follow these strict operational constraints:'
                                . "\n"
                                . '1. GEOGRAPHIC CLUSTERING: Group daily activities that are close to each other. '
                                . 'Minimize driving time and AVOID ZIG-ZAGGING back and forth across the country on the same day. '
                                . 'A traveller must never drive far in one direction and then back the same day.'
                                . "\n"
                                . '2. ACCOMMODATION LOGIC: Locations marked with "is_lodging": true (hotels, apartments, houses, B&Bs, villas) are BASE CAMPS, not 2-hour visits. '
                                . 'A user sleeps there. The accommodation appears at the start of the day (morning departure / checkout) or end of the day (evening return / check-in). '
                                . 'Accommodations REPEAT across consecutive days until the itinerary moves to a new region. '
                                . 'Activities (is_lodging=false) are one-time visits and MUST appear exactly once across the whole plan.'
                                . "\n"
                                . '3. EFFICIENCY: Distribute activities evenly across the available days. Every day must have at least one location if possible — do not leave empty days while others are overloaded.'
                                . "\n"
                                . '4. ORDER WITHIN A DAY: Activities follow a logical geographic route starting from the day\'s base camp '
                                . '(e.g. Hotel → Point A → Point B → Point C, with implicit return to the same Hotel at night).'
                                . "\n\n"
                                . 'You NEVER drop any input location_id and NEVER invent new ones. '
                                . 'You output JSON that strictly matches the provided schema, with no extra prose.',
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
                $bodyExcerpt = mb_substr((string) $response->getContent(false), 0, 400);
                $this->logger->warning('OpenAI itinerary optimizer returned HTTP {status} (model={model})', [
                    'status' => $statusCode,
                    'model' => $this->model,
                    'body' => $bodyExcerpt,
                ]);
                throw new ItineraryOptimizationFailed(sprintf(
                    'OpenAI returned HTTP %d for model "%s". Response: %s',
                    $statusCode,
                    $this->model,
                    $bodyExcerpt !== '' ? $bodyExcerpt : '(empty)'
                ));
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

        $this->logger->info('OpenAI itinerary: raw plan returned by model', [
            'raw_plan' => $parsed['plan'],
        ]);

        $finalPlan = $this->buildFinalPlan($parsed['plan'], $locations, $numberOfDays, $lodgingIds);

        $this->logger->info('OpenAI itinerary: final plan after backend post-processing', [
            'final_plan' => $finalPlan,
        ]);

        return $finalPlan;
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
                'is_lodging' => $loc->isLodging,
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
     * Converts the new structured response (start_accommodation / mid_day_activities / end_accommodation)
     * into the flat map<day, location_id[]> expected by GenerateItineraryService.
     *
     * Ordering per day: start_accommodation → mid_day_activities → end_accommodation
     * (if start == end the accommodation appears only once, at the front).
     *
     * @param array<mixed>             $rawPlan
     * @param ItineraryLocationInput[] $inputLocations
     * @param array<string, true>      $lodgingIds
     *
     * @return array<int, string[]>
     */
    private function buildFinalPlan(array $rawPlan, array $inputLocations, int $numberOfDays, array $lodgingIds): array
    {
        $allValidIds = [];
        foreach ($inputLocations as $loc) {
            $allValidIds[$loc->id] = true;
        }

        $plan            = [];
        $usedActivityIds = [];

        foreach ($rawPlan as $dayEntry) {
            if (!isset($dayEntry['day'])) {
                continue;
            }

            $day = (int) $dayEntry['day'];
            if ($day < 1 || $day > $numberOfDays) {
                $this->logger->warning('OpenAI itinerary: skipping out-of-range day {day}', ['day' => $day]);
                continue;
            }

            $idsForDay     = [];
            $seenOnThisDay = [];

            $addId = function (string $id, bool $isActivity) use (
                &$idsForDay, &$seenOnThisDay, &$usedActivityIds, $allValidIds, $day
            ): void {
                if ($id === '' || !isset($allValidIds[$id]) || isset($seenOnThisDay[$id])) {
                    return;
                }
                if ($isActivity && isset($usedActivityIds[$id])) {
                    $this->logger->info('OpenAI itinerary: activity "{id}" already used — skipped on day {day}', [
                        'id'  => $id,
                        'day' => $day,
                    ]);
                    return;
                }
                $seenOnThisDay[$id] = true;
                if ($isActivity) {
                    $usedActivityIds[$id] = true;
                }
                $idsForDay[] = $id;
            };

            // 1. Start accommodation (always first).
            $startAcc = (string) ($dayEntry['start_accommodation'] ?? '');
            $addId($startAcc, false);

            // 2. Mid-day activities ordered as the AI chose.
            foreach ((array) ($dayEntry['mid_day_activities'] ?? []) as $actId) {
                $addId((string) $actId, true);
            }

            // 3. End accommodation — only if different from start (avoid same-day duplicate).
            $endAcc = (string) ($dayEntry['end_accommodation'] ?? '');
            if ($endAcc !== $startAcc && $endAcc !== '') {
                $addId($endAcc, false);
            }

            if (!empty($idsForDay)) {
                $plan[$day] = $idsForDay;
            }
        }

        // Fallback: activities the AI forgot → spread round-robin across days.
        $assigned = [];
        foreach ($plan as $ids) {
            foreach ($ids as $id) {
                $assigned[$id] = true;
            }
        }

        $missing = [];
        foreach ($inputLocations as $loc) {
            if (!isset($assigned[$loc->id])) {
                $missing[] = $loc;
            }
        }

        if (!empty($missing)) {
            $this->logger->warning('OpenAI itinerary: {count} location(s) missing from plan, appending as fallback', [
                'count' => count($missing),
                'ids'   => array_map(static fn ($l) => $l->id, $missing),
            ]);
            $targetDays = !empty($plan) ? array_keys($plan) : range(1, $numberOfDays);
            sort($targetDays);
            $cursor = 0;
            foreach ($missing as $loc) {
                $d = $targetDays[$cursor % count($targetDays)];
                $plan[$d] = $plan[$d] ?? [];
                if (!in_array($loc->id, $plan[$d], true)) {
                    $plan[$d][] = $loc->id;
                }
                ++$cursor;
            }
        }

        if (!empty($lodgingIds)) {
            $this->ensureLodgingOnEveryDay($plan, $numberOfDays, $lodgingIds);
        }

        ksort($plan);

        return $plan;
    }

    /**
     * Guarantees that every day in [1..numberOfDays] has at least one lodging at position 0
     * (the base camp). When only one lodging exists in the trip it is placed on every day.
     * When several lodgings exist, missing days are forward-filled from the nearest neighbouring
     * day that already has a lodging assigned (preferring the previous day for tie-breaking).
     *
     * @param array<int, string[]>      $plan        passed by reference, mutated in place
     * @param array<string, true>       $lodgingIds  set of location_ids that are lodgings
     */
    private function ensureLodgingOnEveryDay(array &$plan, int $numberOfDays, array $lodgingIds): void
    {
        // Identify the lodging currently assigned to each day (first lodging found in the day's list).
        $lodgingByDay = [];
        foreach ($plan as $day => $ids) {
            foreach ($ids as $id) {
                if (isset($lodgingIds[$id])) {
                    $lodgingByDay[$day] = $id;
                    break;
                }
            }
        }

        $singleLodgingId = count($lodgingIds) === 1 ? array_key_first($lodgingIds) : null;

        for ($day = 1; $day <= $numberOfDays; ++$day) {
            $plan[$day] = $plan[$day] ?? [];

            if (isset($lodgingByDay[$day])) {
                // Lodging present: just make sure it is first in the array (S6 ordering rule).
                $lodgingForDay = $lodgingByDay[$day];
                if ($plan[$day][0] !== $lodgingForDay) {
                    $plan[$day] = array_values(array_filter(
                        $plan[$day],
                        static fn ($id) => $id !== $lodgingForDay
                    ));
                    array_unshift($plan[$day], $lodgingForDay);
                }
                continue;
            }

            // Pick which lodging to assign:
            //  - If there is only one lodging in the whole trip, use it.
            //  - Otherwise forward-fill: nearest neighbour day with a lodging, preferring previous days.
            $chosen = $singleLodgingId;
            if ($chosen === null) {
                $bestDist = PHP_INT_MAX;
                foreach ($lodgingByDay as $otherDay => $lodgingId) {
                    $dist = abs($otherDay - $day);
                    // Strict improvement, OR same distance but "previous" day (otherDay < day) wins.
                    if ($dist < $bestDist
                        || ($dist === $bestDist && $otherDay < $day && ($lodgingByDay[$day - $bestDist] ?? null) !== $lodgingId)
                    ) {
                        $bestDist = $dist;
                        $chosen = $lodgingId;
                    }
                }
            }

            if ($chosen !== null) {
                array_unshift($plan[$day], $chosen);
                $lodgingByDay[$day] = $chosen;
                $this->logger->info('OpenAI itinerary: filled missing lodging for day {day} with {id}', [
                    'day' => $day,
                    'id' => $chosen,
                ]);
            }
        }
    }
}
