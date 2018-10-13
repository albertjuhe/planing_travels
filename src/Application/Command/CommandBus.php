<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 08/10/2018
 * Time: 08:02
 */

namespace App\Application\Command;


use App\Application\UseCases\Travel\UpdateTravelService;
use App\Application\UseCases\Travel\AddTravelService;
use App\Infrastructure\TravelBundle\Repository\DoctrineTravelRepository;

/**
 * Class CommandBus
 * @package App\Application\Command
 */
class CommandBus
{

    /** @var array  */
    private $handlers = [];
    /** @var  DoctrineTravelRepository */
    private $doctrineTravelRepository;

    /**
     * CommandBus constructor.
     * @param DoctrineTravelRepository $doctrineTravelRepository
     */
    public function __construct(DoctrineTravelRepository $doctrineTravelRepository)
    {
        $this->handlers = [];
        $this->doctrineTravelRepository = $doctrineTravelRepository;

        $this->addHandler(UpdateTravelCommand::class, new UpdateTravelService($this->doctrineTravelRepository));
        $this->addHandler(AddTravelCommand::class, new AddTravelService($this->doctrineTravelRepository));

    }

    public function addHandler($commandName, $commandHandler) {
        $this->handlers[$commandName] = $commandHandler;

    }

    public function handle($command) {
        $commandHandler = $this->handlers[get_class($command)];
        if ($commandHandler === null) {
            throw \Exception();
        }
        $commandHandler->execute($command); //handle or execute

    }

}