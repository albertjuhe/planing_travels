<?php
namespace App\Application\UseCases\Travel;

use App\Application\Command\Travel\ShowTravelBySlugCommand;
use App\Domain\Travel\Exceptions\TravelDoesntExists;
use App\Domain\Travel\Repository\TravelRepository;

class ShowTravelService
{
    /** @var TravelRepository */
    private $travelRepository;

    /**
     * ShowTravelService constructor.
     * @param TravelRepository $travelRepository
     */
    public function __construct(TravelRepository $travelRepository)
    {
        $this->travelRepository = $travelRepository;
    }

    /**
     * @param ShowTravelBySlugCommand $comamnd
     * @return \App\Domain\Travel\Model\Travel
     * @throws TravelDoesntExists
     */
    public function execute(ShowTravelBySlugCommand $comamnd) {
        if ($comamnd->getSlug() === null) throw new TravelDoesntExists();
        return $this->travelRepository->ofSlugOrFail($comamnd->getSlug());
    }

}