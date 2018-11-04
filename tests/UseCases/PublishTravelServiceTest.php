<?php


namespace App\Tests\UseCases;


use App\Application\Command\Travel\PublishTravelCommand;
use App\Application\UseCases\Travel\PublishTravelService;
use App\Application\UseCases\Travel\UpdateTravelService;
use App\Domain\Common\Model\TriggerEventsTrait;
use App\Domain\Event\DomainEvent;
use App\Domain\Event\DomainEventPublisher;
use App\Domain\Event\DomainEventSubscriber;
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

        /** @var  DomainEventAllSubscriber */
        $subscriber = DomainEventPublisher::instance()->ofId($this->idSubscriber);
        $this->assertCount(1,$subscriber->getEvents());
    }
}

/**
 * This subscriber is subscribed to all events
 * Class GeneralEventSubscriber
 * @package App\Tests\UseCases
 */
class DomainEventAllSubscriber implements DomainEventSubscriber {
    use TriggerEventsTrait;

    public function handle(DomainEvent $domainEvent)
    {
        $this->trigger($domainEvent);
    }

    public function isSubscribedTo(DomainEvent $domainEvent)
    {
       return true;
    }

}
