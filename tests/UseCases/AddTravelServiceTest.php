<?php

namespace App\Tests\UseCases;

use App\Domain\User\Exceptions\UserDoesntExists;
use App\Infrastructure\UserBundle\Repository\InMemoryUserRepository;
use PHPUnit\Framework\TestCase;
use App\Application\UseCases\Travel\AddTravelService;
use App\Infrastructure\TravelBundle\Repository\InMemoryTravelRepository;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;
use App\Application\Command\Travel\AddTravelCommand;

class AddTravelServiceTest extends TestCase
{
    const TRAVELID = 3;
    /** @var InMemoryTravelRepository */
    private $travelRepository;
    /** @var InMemoryUserRepository */
    private $userRepository;

    public function setUp()
    {
        $this->travelRepository = new InMemoryTravelRepository();
        $this->userRepository = new InMemoryUserRepository();
    }

    /**
     * Add new travel.
     */
    public function testAddTravel()
    {
        $travel = new Travel();
        $travel->setId(self::TRAVELID);
        $user = User::byId(1);

        $addTravelService = new AddTravelService($this->travelRepository, $this->userRepository);
        $command = new AddTravelCommand($travel, $user);
        $addTravelService->handle($command);

        $newTravel = $this->travelRepository->getTravelById(self::TRAVELID);
        $this->assertEquals($newTravel->getId(), $travel->getId());
    }

    public function testAddTravelWithInvalidUser()
    {
        $this->expectExceptionObject(new UserDoesntExists());
        $travel = new Travel();
        $user = User::byId(0);

        $addTravelService = new AddTravelService($this->travelRepository, $this->userRepository);
        $command = new AddTravelCommand($travel, $user);
        $addTravelService->handle($command);
    }
}
