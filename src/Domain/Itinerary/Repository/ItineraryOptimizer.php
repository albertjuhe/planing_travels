<?php

namespace App\Domain\Itinerary\Repository;

use App\Domain\Itinerary\Model\ItineraryLocationInput;

interface ItineraryOptimizer
{
    /**
     * Distribute a set of locations across the available trip days, grouping them
     * geographically to minimise driving time.
     *
     * @param ItineraryLocationInput[] $locations Locations to distribute.
     * @param int                      $numberOfDays Total number of trip days (≥ 1).
     * @param \DateTime|null           $startDate First day of the trip (used so the AI knows day-of-week).
     * @param string                   $destinationName Human-readable trip title / country name.
     * @param string                   $locale BCP-47 locale of the requesting user (e.g. "en", "es").
     *
     * @return array<int, string[]> Map of day number (1-based) to ordered array of location IDs.
     */
    public function optimize(
        array $locations,
        int $numberOfDays,
        ?\DateTime $startDate,
        string $destinationName,
        string $locale,
        string $additionalNotes = '',
    ): array;
}
