<?php

namespace App\Tests\Application\UseCases\Travel;

use App\Application\Query\Travel\BestTravelsListQuery;
use App\Application\UseCases\Travel\GetBestTravelsOrderedByService;
use App\Tests\Infrastructure\TravelBundle\Repository\InMemoryElasticSearchRepository;
use App\Tests\Infrastructure\TravelBundle\Repository\InMemoryTravelRepository;
use App\Tests\Domain\Travel\Model\TravelMother;
use PHPUnit\Framework\TestCase;

class GetBestTravelsOrderedByServiceTest extends TestCase
{
    private InMemoryElasticSearchRepository $elasticRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->elasticRepository = new InMemoryElasticSearchRepository();
    }

    public function testReturnsEmptyWhenRepositoryIsEmpty(): void
    {
        $service = new GetBestTravelsOrderedByService($this->elasticRepository);
        $results = $service(new BestTravelsListQuery(10, 'stars'));

        $this->assertEmpty($results);
    }

    public function testReturnsOnlyPublishedTravels(): void
    {
        $published = TravelMother::random();
        $published->setStars(5);
        $published->publish();

        $draft = TravelMother::random();
        $draft->setStars(10);

        $this->elasticRepository->save($published);
        $this->elasticRepository->save($draft);

        $service = new GetBestTravelsOrderedByService($this->elasticRepository);
        $results = $service(new BestTravelsListQuery(10, 'stars'));

        $this->assertCount(1, $results);
        $this->assertSame($published->getId()->id(), $results[0]['id']);
    }

    public function testRespectsMaxResultsLimit(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            $travel = TravelMother::random();
            $travel->setStars($i);
            $travel->publish();
            $this->elasticRepository->save($travel);
        }

        $service = new GetBestTravelsOrderedByService($this->elasticRepository);
        $results = $service(new BestTravelsListQuery(5, 'stars'));

        $this->assertCount(5, $results);
    }

    public function testOrdersByStarsDescending(): void
    {
        $low = TravelMother::random();
        $low->setStars(1);
        $low->publish();

        $high = TravelMother::random();
        $high->setStars(5);
        $high->publish();

        $mid = TravelMother::random();
        $mid->setStars(3);
        $mid->publish();

        $this->elasticRepository->save($low);
        $this->elasticRepository->save($high);
        $this->elasticRepository->save($mid);

        $service = new GetBestTravelsOrderedByService($this->elasticRepository);
        $results = $service(new BestTravelsListQuery(10, 'stars'));

        $this->assertSame(5, $results[0]['stars']);
        $this->assertSame(3, $results[1]['stars']);
        $this->assertSame(1, $results[2]['stars']);
    }

    public function testMaxResultsZeroReturnsEmpty(): void
    {
        $travel = TravelMother::random();
        $travel->setStars(5);
        $travel->publish();
        $this->elasticRepository->save($travel);

        $service = new GetBestTravelsOrderedByService($this->elasticRepository);
        $results = $service(new BestTravelsListQuery(0, 'stars'));

        $this->assertEmpty($results);
    }
}
