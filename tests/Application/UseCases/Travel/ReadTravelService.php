<?php

namespace App\Tests\Application\UseCases\Travel;

use App\Infrastructure\TravelBundle\Repository\ElasticSearchReadModelRepository;
use App\Infrastructure\UserBundle\Repository\DoctrineUserRepository;
use PHPUnit\Framework\TestCase;

class ReadTravelService extends TestCase
{
    protected $travelRepository;
    protected $userRepository;

    public function setUp()
    {
        $this->travelRepository = $this->createMock(ElasticSearchReadModelRepository::class);
        $this->userRepository = $this->createMock(DoctrineUserRepository::class);
    }
}
