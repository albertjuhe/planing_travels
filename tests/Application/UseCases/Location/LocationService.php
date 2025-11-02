<?php

namespace App\Tests\Application\UseCases\Location;

use App\Domain\Location\Repository\LocationRepository;
use App\Domain\Mark\Repository\MarkRepository;
use App\Domain\Travel\Repository\TravelRepository;
use App\Domain\TypeLocation\Repository\TypeLocationRepository;
use App\Domain\User\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

class LocationService extends TestCase
{
    protected $travelRepository;
    protected $userRepository;
    protected $markRepository;
    protected $locationRepository;
    protected $typeLocationRepository;

    public function setUp()
    {
        $this->travelRepository = $this->createMock(TravelRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->markRepository = $this->createMock(MarkRepository::class);
        $this->locationRepository = $this->createMock(LocationRepository::class);
        $this->typeLocationRepository = $this->createMock(TypeLocationRepository::class);
    }
}
