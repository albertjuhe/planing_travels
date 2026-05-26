<?php

namespace App\Domain\Itinerary\Repository;

use App\Domain\Itinerary\Model\SuggestedLocation;

interface LocationSuggester
{
    /**
     * Ask the AI for a list of recommended locations for a trip.
     *
     * @param string      $destination  Human-readable destination name (e.g. "Toscana, Italia").
     * @param int         $numberOfDays Duration of the trip.
     * @param string      $locale       BCP-47 locale of the requesting user.
     * @param string      $preferences  Optional free-text user preferences.
     * @param string[]    $existing     Titles of locations already added (to avoid duplicates).
     *
     * @return SuggestedLocation[]
     */
    public function suggest(
        string $destination,
        int    $numberOfDays,
        string $locale,
        string $preferences = '',
        array  $existing = [],
    ): array;
}
