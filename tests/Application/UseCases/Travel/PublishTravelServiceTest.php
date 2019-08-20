<?php

namespace App\Tests\Application\UseCases\Travel;

use App\Application\Command\Travel\PublishTravelCommand;
use App\Application\UseCases\Travel\PublishTravelService;
use App\Application\UseCases\Travel\UpdateTravelService;
use App\Domain\Event\DomainEventPublisher;
use App\Domain\Travel\Exceptions\NotAllowedToPublishTravel;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;
use App\Infrastructure\TravelBundle\Repository\InMemoryTravelRepository;
use App\Tests\Subscriber\DomainEventAllSubscriber;
use PHPUnit\Framework\TestCase;
use App\Infrastructure\UserBundle\Repository\InMemoryUserRepository;

class PublishTravelServiceTest extends TestCase
{
    /** @var InMemoryTravelRepository */
    private $travelRepository;
    /** @var InMemoryUserRepository */
    private $userRepository;
    /** @var int */
    private $idSubscriber;

    protected function setUp()
    {
        $this->travelRepository = new InMemoryTravelRepository();
        $this->userRepository = new InMemoryUserRepository();
        $this->idSubscriber = DomainEventPublisher::instance()->subscribe(new DomainEventAllSubscriber());
    }

    public function testPublishTravel()
    {
        /** @var Travel $travel */
        $travel = new Travel();
        $travel->setSlug('test-travel');
        /** @var User $user */
        $user = User::byId(1);
        $travel->setUser($user);
        $this->travelRepository->save($travel);

        $this->assertEquals($travel->getStatus(), Travel::TRAVEL_DRAFT);

        /** @var PublishTravelCommand $updateTravelCommand */
        $publishTravelCommand = new PublishTravelCommand($travel->getSlug(), $user);
        /** @var UpdateTravelService */
        $publishTravelService = new PublishTravelService($this->travelRepository, $this->userRepository);
        $publishTravelService->handle($publishTravelCommand);

        $travelPublished = $this->travelRepository->getTravelById(1);
        $this->assertEquals($travelPublished->getStatus(), Travel::TRAVEL_PUBLISHED);

        /** @var DomainEventAllSubscriber */
        $subscriber = DomainEventPublisher::instance()->ofId($this->idSubscriber);
        $this->assertCount(1, $subscriber->getEvents());
    }

    public function testPublishNotAllowedException()
    {
        $this->expectException(NotAllowedToPublishTravel::class);

        /** @var Travel $travel */
        $travel = new Travel();
        $travel->setSlug('test-travel');
        /** @var User $user */
        $user = User::byId(1);
        $user2 = User::byId(2);

        $travel->setUser($user2);
        $this->travelRepository->save($travel);

        $this->assertEquals($travel->getStatus(), Travel::TRAVEL_DRAFT);

        /** @var PublishTravelCommand $updateTravelCommand */
        $publishTravelCommand = new PublishTravelCommand($travel->getSlug(), $user);
        /** @var UpdateTravelService */
        $publishTravelService = new PublishTravelService($this->travelRepository, $this->userRepository);
        $publishTravelService->handle($publishTravelCommand);
    }
}
