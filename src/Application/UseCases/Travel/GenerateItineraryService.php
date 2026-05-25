<?php

namespace App\Application\UseCases\Travel;

use App\Application\Command\Travel\GenerateItineraryCommand;
use App\Application\UseCases\UsesCasesService;
use App\Domain\Itinerary\Exceptions\TravelHasNoDates;
use App\Domain\Itinerary\Model\ItineraryLocationInput;
use App\Domain\Itinerary\Repository\ItineraryOptimizer;
use App\Domain\Location\Model\Location;
use App\Domain\Travel\Exceptions\InvalidTravelUser;
use App\Domain\Travel\Exceptions\TravelDoesntExists;
use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\Repository\TravelRepository;
use App\Infrastructure\WebSocket\WebSocketNotifier;
use Doctrine\ORM\EntityManagerInterface;

class GenerateItineraryService implements UsesCasesService
{
    public function __construct(
        private readonly TravelRepository $travelRepository,
        private readonly ItineraryOptimizer $optimizer,
        private readonly EntityManagerInterface $em,
        private readonly WebSocketNotifier $webSocketNotifier,
    ) {
    }

    /**
     * @return array{daysAffected: int, locationsAssigned: int}
     */
    public function __invoke(GenerateItineraryCommand $command): array
    {
        try {
            $travel = $this->travelRepository->ofIdOrFail($command->travelId);
        } catch (TravelDoesntExists $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new TravelDoesntExists('Travel not found: ' . $e->getMessage());
        }

        $this->assertUserCanEdit($travel, $command->userId);

        if ($travel->getStartAt() === null || $travel->getEndAt() === null) {
            throw new TravelHasNoDates('The travel has no start or end date set.');
        }

        $startAt = $travel->getStartAt();
        $endAt = $travel->getEndAt();
        $numberOfDays = (int) floor(($endAt->getTimestamp() - $startAt->getTimestamp()) / 86400) + 1;
        $numberOfDays = max(1, $numberOfDays);

        $allLocations = $travel->getLocation()->toArray();

        if ($command->mode === 'all') {
            $locationsToOptimize = $allLocations;
            $needsOrphanFlush = false;
            foreach ($locationsToOptimize as $loc) {
                if ($loc->hasAnyVisitDate()) {
                    $needsOrphanFlush = true;
                }
                $loc->clearVisitDates();
                $loc->setVisitAt(null);
            }
            // Flush orphan removals BEFORE inserting new visit dates to avoid
            // UNIQUE(location_id, visit_date) constraint violation.
            if ($needsOrphanFlush) {
                $this->em->flush();
            }
        } else {
            $locationsToOptimize = array_values(array_filter(
                $allLocations,
                static fn (Location $loc) => !$loc->hasAnyVisitDate()
            ));
        }

        if (empty($locationsToOptimize)) {
            return ['daysAffected' => 0, 'locationsAssigned' => 0];
        }

        $inputs = $this->buildInputs($locationsToOptimize);

        $plan = $this->optimizer->optimize(
            $inputs,
            $numberOfDays,
            $startAt,
            $travel->getTitle() ?? '',
            $command->locale,
            $command->additionalNotes,
        );

        $locationById = [];
        foreach ($locationsToOptimize as $loc) {
            $locationById[$loc->getId()->id()] = $loc;
        }

        $daysAffected = 0;
        $locationsAssigned = 0;
        $modifiedLocations = [];

        foreach ($plan as $dayNumber => $locationIds) {
            $targetDate = clone $startAt;
            $targetDate->modify(sprintf('+%d days', $dayNumber - 1));

            $position = 0;
            foreach ($locationIds as $locationId) {
                if (!isset($locationById[$locationId])) {
                    continue;
                }
                $loc = $locationById[$locationId];
                $visitDate = $loc->addVisitDate($targetDate);
                $visitDate->setPosition($position);
                $loc->setVisitAt($targetDate);

                $modifiedLocations[$locationId] = $loc;

                ++$position;
                ++$locationsAssigned;
            }

            if ($position > 0) {
                ++$daysAffected;
            }
        }

        $this->em->flush();

        // Notify WebSocket clients AFTER flush so they see consistent data.
        foreach ($modifiedLocations as $loc) {
            try {
                $this->webSocketNotifier->notifyVisitDatesChanged(
                    $travel->getId()->id(),
                    $loc->getId()->id(),
                    $loc->getVisitDateStrings(),
                    $command->userId,
                    'AI Planner',
                );
            } catch (\Throwable) {
                // WebSocket failures must never break the use case.
            }
        }

        return ['daysAffected' => $daysAffected, 'locationsAssigned' => $locationsAssigned];
    }

    /**
     * @param Location[] $locations
     *
     * @return ItineraryLocationInput[]
     */
    private function buildInputs(array $locations): array
    {
        $inputs = [];
        foreach ($locations as $loc) {
            $lat = null;
            $lng = null;
            $mark = $loc->getMark();
            if ($mark !== null) {
                $geo = $mark->getGeoLocation();
                $rawLat = $geo->lat();
                $rawLng = $geo->lng();
                if ($rawLat != 0 || $rawLng != 0) {
                    $lat = (float) $rawLat;
                    $lng = (float) $rawLng;
                }
            }

            $typeName = null;
            $typeIcon = null;
            try {
                $typeLocation = $loc->getTypeLocation();
                $typeName = $typeLocation?->getTitle();
                $typeIcon = $typeLocation?->getIcon();
            } catch (\Throwable) {
            }

            $isLodging = $this->isLodgingType($typeName, $typeIcon)
                || $this->isLodgingByTitle((string) $loc->getTitle());

            $inputs[] = new ItineraryLocationInput(
                id: $loc->getId()->id(),
                title: $loc->getTitle() ?? '',
                description: $loc->getDescription(),
                typeName: $typeName,
                lat: $lat,
                lng: $lng,
                isLodging: $isLodging,
            );
        }

        return $inputs;
    }

    private function assertUserCanEdit(Travel $travel, string $userId): void
    {
        $ownerId = (string) $travel->getUser()->getId()->id();
        if ($ownerId === $userId) {
            return;
        }

        foreach ($travel->getSharedusers() as $sharedUser) {
            if ((string) $sharedUser->getId()->id() === $userId) {
                return;
            }
        }

        throw new InvalidTravelUser();
    }

    private function isLodgingType(?string $typeTitle, ?string $icon): bool
    {
        // The TypeLocation title is the source of truth (explicit user choice).
        $title = strtolower(trim((string) $typeTitle));
        if (in_array($title, ['hotel', 'house', 'hostel', 'hostal', 'apartment', 'apartamento', 'b&b', 'bnb', 'lodging', 'accommodation'], true)) {
            return true;
        }

        // Icon fallback: ONLY fa-bed is exclusive to lodging.
        // We deliberately do NOT match fa-building because the seeded "City" type also uses it,
        // so cities would be wrongly classified as lodgings.
        $iconLower = strtolower((string) $icon);
        if (str_contains($iconLower, 'fa-bed')) {
            return true;
        }

        return false;
    }

    private function isLodgingByTitle(string $title): bool
    {
        $needle = strtolower($title);
        foreach (['hotel', 'hostel', 'hostal', 'airbnb', 'b&b', 'bed and breakfast', 'guesthouse', 'guest house', 'apartment', 'apartamento', 'apartament', 'pension', 'pensión', 'lodge'] as $keyword) {
            if (str_contains($needle, $keyword)) {
                return true;
            }
        }
        return false;
    }
}
