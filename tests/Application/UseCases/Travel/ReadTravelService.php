<?php

namespace App\Tests\Application\UseCases\Travel;

use App\Infrastructure\TravelBundle\Repository\ElasticSearchReadModelRepository;
use PHPUnit\Framework\TestCase;

class ReadTravelService extends TestCase
{
    protected $travelRepository;

    public function setUp(): void
    {
        $this->travelRepository = $this->createMock(ElasticSearchReadModelRepository::class);
    }
}
