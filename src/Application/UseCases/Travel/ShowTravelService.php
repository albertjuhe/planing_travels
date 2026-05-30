<?php

namespace App\Application\UseCases\Travel;

use App\Application\Query\Travel\ShowTravelBySlugQuery;
use App\Application\UseCases\UsesCasesService;
use App\Domain\Travel\Exceptions\TravelDoesntExists;
use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\Repository\TravelRepository;
use App\Domain\User\ValueObject\UserId;

class ShowTravelService implements UsesCasesService
{
    public function __construct(
        private readonly TravelRepository $travelRepository,
    ) {
    }

    public function __invoke(ShowTravelBySlugQuery $query): Travel
    {
        $travel = $this->travelRepository->ofSlugOrFail($query->getSlug());

        if (null === $travel) {
            throw new TravelDoesntExists();
        }

        $userId = $query->getUserId();
        $isOwner = false;
        $isSharedUser = false;

        if ($userId !== null) {
            $currentUserId = new UserId($userId);
            $isOwner = $travel->getUser()->getId()->equalsTo($currentUserId);

            if (!$isOwner) {
                foreach ($travel->getSharedusers() as $sharedUser) {
                    if ($sharedUser->getId()->equalsTo($currentUserId)) {
                        $isSharedUser = true;
                        break;
                    }
                }
            }
        }

        if (!$isOwner && !$isSharedUser) {
            $travel->incrementWatch();
            $this->travelRepository->save($travel);
            $this->travelRepository->flush();
        }

        return $travel;
    }
}
