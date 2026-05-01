<?php

namespace App\Application\UseCases\Location;

use App\Application\Query\Location\GetLocationsByTravelQuery;
use App\Application\UseCases\UsesCasesService;
use App\Domain\Travel\Exceptions\TravelDoesntExists;
use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\Repository\TravelRepository;

class GetLocationsByTravelService implements usesCasesService
{
    /**
     * @var TravelRepository;
     */
    private $travelRepository;

    /**
     * GetAllMyTravels constructor.
     *
     * @param TravelRepository $travelRepository
     */
    public function __construct(TravelRepository $travelRepository)
    {
        $this->travelRepository = $travelRepository;
    }

    /**
     * @return mixed
     */
    public function __invoke(GetLocationsByTravelQuery $query)
    {
        $travelId = $query->getTravel();

        /** @var Travel $travel */
        $travel = $this->travelRepository->ofIdOrFail($travelId);

        if (!$travel instanceof Travel) {
            throw new TravelDoesntExists();
        }

        // Get locations with all relations in a single query
        $locationRepository = $this->travelRepository->_em->getRepository(\App\Domain\Location\Model\Location::class);
        $locations = $locationRepository->createQueryBuilder('l')
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
