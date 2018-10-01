<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 01/10/2018
 * Time: 07:13
 */

namespace App\Application\UseCases\Travel;

use App\Domain\Travel\Repository\TravelRepository;


class GetBestTravelsOrderedByService
{
    /**
     * @var TravelRepository;
     */
    private $travelRepository;

    /**
     * GetAllMyTravels constructor.
     * @param TravelRepository $travelRepository
     */
    public function __construct(TravelRepository $travelRepository)
    {
        $this->travelRepository = $travelRepository;
    }

    /**
     * @param int $numberMaxOfTravels
     * @param $orderedBy
     * @return mixed
     */
    public function execute(int $numberMaxOfTravels = 10, $orderedBy = 'star') {
        return $this->travelRepository->TravelsAllOrderedBy($numberMaxOfTravels);
    }
}