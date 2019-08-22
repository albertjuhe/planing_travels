<?php

namespace App\Tests\Application\UseCases\Travel;

use App\Domain\Event\DomainEventPublisher;
use App\Infrastructure\TravelBundle\Repository\InMemoryTravelRepository;
use App\Infrastructure\UserBundle\Repository\InMemoryUserRepository;
use App\Tests\Subscriber\DomainEventAllSubscriber;
use PHPUnit\Framework\TestCase;

class TravelService extends TestCase
{
    protected $travelRepository;
    protected $userRepository;
    protected $idSubscriber;

    public function setUp()
    {
        $this->travelRepository = new InMemoryTravelRepository();
        $this->userRepository = new InMemoryUserRepository();
        $this->travelRepository->loadData();
        $this->idSubscriber = DomainEventPublisher::instance()->subscribe(new DomainEventAllSubscriber());
    }
}
