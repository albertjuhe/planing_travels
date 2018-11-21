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
     * @param ShowTravelBySlugCommand $command
     * @return \App\Domain\Travel\Model\Travel
     * @throws TravelDoesntExists
     */
    public function handle(ShowTravelBySlugCommand $command)
    {
        if ($command->getSlug() === null) throw new TravelDoesntExists();
        return $this->travelRepository->ofSlugOrFail($command->getSlug());
    }

}