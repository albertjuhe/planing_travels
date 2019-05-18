<?php

namespace App\Tests\UseCases;

use App\Infrastructure\TravelBundle\Repository\InMemoryTravelRepository;
use App\Infrastructure\UserBundle\Repository\InMemoryUserRepository;
use PHPUnit\Framework\TestCase;

class TravelServiceTest extends TestCase
{
    protected $travelRepository;
    protected $userRepository;

    public function setUp()
    {
        $this->travelRepository = new InMemoryTravelRepository();
        $this->userRepository = new InMemoryUserRepository();
        $this->travelRepository->loadData();
    }
}
