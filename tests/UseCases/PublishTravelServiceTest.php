<?php


namespace App\Tests\UseCases;


use App\Application\Command\Travel\PublishTravelCommand;
use App\Application\UseCases\Travel\PublishTravelService;
use App\Application\UseCases\Travel\UpdateTravelService;
use App\Domain\Event\DomainEventPublisher;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;
use App\Infrastructure\TravelBundle\Repository\InMemoryTravelRepository;
use PHPUnit\Framework\TestCase;
use App\Infrastructure\UserBundle\Repository\InMemoryUserRepository;

class PublishTravelServiceTest extends TestCase
{
    const TRAVELID = 1;

    /** @var InMemoryTravelRepository */
    private $travelRepository;
    /** @var InMemoryUserRepository */
    private $userRepository;

    protected function setUp()
    {
        $this->travelRepository = new InMemoryTravelRepository();
        $this->userRepository = new InMemoryUserRepository();
    }

    public function testPublishTravel()
    {
        /** @var Travel $travel */
        $travel = new Travel();
        $travel->setId(self::TRAVELID);
        $travel->setSlug('test-travel');
        /** @var User $user */
        $user = User::byId(1);
        $travel->setUser($user);
        $this->travelRepository->save($travel);

        $this->assertEquals($travel->getStatus(),Travel::TRAVEL_DRAFT);

        /** @var PublishTravelCommand $updateTravelCommand */
        $publishTravelCommand = new PublishTravelCommand($travel->getSlug(),$user);
        /** @var UpdateTravelService */
        $publishTravelService = new PublishTravelService($this->travelRepository,$this->userRepository);
        $publishTravelService->execute($publishTravelCommand);

        $travelPublished = $this->travelRepository->getTravelById(1);
        $this->assertEquals($travelPublished->getStatus(),Travel::TRAVEL_PUBLISHED);

    }
}
