<?php

namespace App\Application\UseCases\Location;

use App\Application\Query\Location\GetLocationsByTravelQuery;
use App\Application\UseCases\UsesCasesService;
use App\Domain\Location\Model\Location;
use App\Domain\Travel\Exceptions\TravelDoesntExists;
use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\Repository\TravelRepository;
use Doctrine\ORM\EntityManagerInterface;

class GetLocationsByTravelService implements usesCasesService
{
    public function __construct(
        private TravelRepository $travelRepository,
        private EntityManagerInterface $em
    ) {
    }

    public function __invoke(GetLocationsByTravelQuery $query)
    {
        $travelId = $query->getTravel();

        /** @var Travel $travel */
        $travel = $this->travelRepository->ofIdOrFail($travelId);

        if (!$travel instanceof Travel) {
            throw new TravelDoesntExists();
        }

        $locations = $this->em->getRepository(Location::class)
            ->createQueryBuilder('l')
            ->select('l', 'm', 'tl', 'vd', 'i', 'n')
            ->leftJoin('l.mark', 'm')
            ->leftJoin('l.typeLocation', 'tl')
            ->leftJoin('l.visitDates', 'vd')
            ->leftJoin('l.images', 'i')
            ->leftJoin('l.notas', 'n')
            ->where('l.travel = :travelId')
            ->setParameter('travelId', $travelId)
            ->getQuery()
            ->getResult();

        $result = [];
        foreach ($locations as $location) {
            $result[] = $location->toArray();
        }

        return $result;
    }
}
