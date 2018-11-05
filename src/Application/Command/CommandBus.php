<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 08/10/2018
 * Time: 08:02
 */

namespace App\Application\Command;

use App\Application\Command\Travel\PublishTravelCommand;
use App\Application\UseCases\Travel\GetBestTravelsOrderedByService;
use App\Application\UseCases\Travel\PublishTravelService;
use App\Application\UseCases\Travel\ShowTravelService;
use App\Application\UseCases\Travel\UpdateTravelService;
use App\Application\UseCases\Travel\AddTravelService;
use App\Infrastructure\TravelBundle\Repository\DoctrineTravelRepository;
use App\Application\Command\Travel\UpdateTravelCommand;
use App\Application\Command\Travel\AddTravelCommand;
use App\Application\Command\Travel\BestTravelsListCommand;
use App\Application\Command\Travel\ShowTravelBySlugCommand;
use App\Infrastructure\UserBundle\Repository\DoctrineUserRepository;

/**
 * Class CommandBus
 * @package App\Application\Command
 */
class CommandBus
{

    /** @var array */
    private $handlers = [];
    /** @var  DoctrineTravelRepository */
    private $doctrineTravelRepository;
    /** @var DoctrineUserRepository */
    private $doctrineUserRepository;

     /**
     * CommandBus constructor.
     * @param DoctrineTravelRepository $doctrineTravelRepository
     * @param DoctrineUserRepository $doctrineUserRepository
      */
    public function __construct(DoctrineTravelRepository $doctrineTravelRepository,
                                DoctrineUserRepository $doctrineUserRepository)
    {
        //TODO Refactor to Tactician
        $this->handlers = [];
        $this->doctrineTravelRepository = $doctrineTravelRepository;
        $this->doctrineUserRepository = $doctrineUserRepository;

        $this->addHandler(UpdateTravelCommand::class, new UpdateTravelService($this->doctrineTravelRepository));
        $this->addHandler(AddTravelCommand::class, new AddTravelService($this->doctrineTravelRepository,
            $this->doctrineUserRepository));
        $this->addHandler(PublishTravelCommand::class, new PublishTravelService($this->doctrineTravelRepository,
            $this->doctrineUserRepository));
        $this->addHandler(BestTravelsListCommand::class, new GetBestTravelsOrderedByService($this->doctrineTravelRepository));
        $this->addHandler(ShowTravelBySlugCommand::class, new ShowTravelService($this->doctrineTravelRepository));
    }

    public function addHandler($commandName, $commandHandler)
    {
        $this->handlers[$commandName] = $commandHandler;

    }

    public function handle($command)
    {
        $commandHandler = $this->handlers[get_class($command)];
        if ($commandHandler === null) {
            throw \Exception();
        }
        return $commandHandler->execute($command); //handle or execute

    }

}