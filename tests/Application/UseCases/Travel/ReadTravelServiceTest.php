<?php

namespace App\Tests\Application\UseCases\Travel;

use App\Infrastructure\TravelBundle\Repository\ElasticSearchReadModelRepository;
use PHPUnit\Framework\TestCase;

class ReadTravelServiceTest extends TestCase
{
    protected $travelRepository;

    public function setUp()
    {
        $this->travelRepository = $this->createMock(ElasticSearchReadModelRepository::class);
    }
}
