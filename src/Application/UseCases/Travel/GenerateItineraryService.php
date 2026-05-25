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
        $travel = $this->travelRepository->find($command->travelId);
        if ($travel === null) {
            throw new TravelDoesntExists();
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
            foreach ($locationsToOptimize as $loc) {
                $loc->clearVisitDates();
                $loc->setVisitAt(null);
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
        );

        $locationById = [];
        foreach ($locationsToOptimize as $loc) {
            $locationById[$loc->getId()->id()] = $loc;
        }

        $daysAffected = 0;
        $locationsAssigned = 0;

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

                $this->webSocketNotifier->notifyVisitDatesChanged(
                    $travel->getId()->id(),
                    $loc->getId()->id(),
                    $loc->getVisitDateStrings(),
                    $command->userId,
                    'AI Planner',
                );

                ++$position;
                ++$locationsAssigned;
            }

            if ($position > 0) {
                ++$daysAffected;
            }
        }

        $this->em->flush();

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
            try {
                $typeLocation = $loc->getTypeLocation();
                $typeName = $typeLocation !== null ? $typeLocation->getTitle() : null;
            } catch (\Throwable) {
            }

            $inputs[] = new ItineraryLocationInput(
                id: $loc->getId()->id(),
                title: $loc->getTitle() ?? '',
                description: $loc->getDescription(),
                typeName: $typeName,
                lat: $lat,
                lng: $lng,
            );
        }

        return $inputs;
    }

    private function assertUserCanEdit(\App\Domain\Travel\Model\Travel $travel, string $userId): void
    {
        $ownerId = $travel->getUser()->getId()->id();
        if ($ownerId === $userId) {
            return;
        }

        foreach ($travel->getSharedusers() as $sharedUser) {
            if ($sharedUser->getId()->id() === $userId) {
                return;
            }
        }

        throw new InvalidTravelUser();
    }
}
