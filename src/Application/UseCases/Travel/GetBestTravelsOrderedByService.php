<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 01/10/2018
 * Time: 07:13
 */

namespace App\Application\UseCases\Travel;

use App\Domain\Travel\Repository\TravelRepository;
use App\Application\Command\Travel\BestTravelsListCommand;

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
     * @param BestTravelsListCommand $command
     * @return mixed
     */
    public function execute(BestTravelsListCommand $command) {
        $numberMaxOfTravels = $command->getNumberMaxOfTravels();
        $orderedBy = $command->getOrderedBy();
        return $this->travelRepository->TravelsAllOrderedBy($numberMaxOfTravels);
    }
}