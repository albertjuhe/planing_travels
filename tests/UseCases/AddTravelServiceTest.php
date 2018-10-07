<?php


namespace App\Tests\UseCases;

use PHPUnit\Framework\TestCase;
use App\Application\UseCases\Travel\AddTravelService;
use App\Infrastructure\TravelBundle\Repository\InMemoryTravelRepository;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;

class AddTravelServiceTest extends TestCase
{
    const TRAVELID = 3;
    private $travelRepository;

    public function setUp()
    {
     $this->travelRepository = new InMemoryTravelRepository();
    }

    public function testAddTravel() {
        $travel = new Travel();
        $travel->setId(self::TRAVELID);
        $travel->setUser(User::fromId(1));

        $addTravelService = new AddTravelService($this->travelRepository);
        $addTravelService->add($travel);

        $newTravel = $this->travelRepository->getTravelById(self::TRAVELID);
        $this->assertEquals($newTravel->getId(), $travel->getId());
    }
}