<?php

namespace App\Tests\Application\UseCases\Travel;

use App\Application\UseCases\Travel\GetBestTravelsOrderedByService;
use App\Application\UseCases\Travel\PopulateIndexer;
use App\Application\Query\Travel\BestTravelsListQuery;
use App\Domain\Travel\Model\Travel;
use App\Tests\Domain\Travel\Model\TravelMother;
use App\Tests\Domain\User\Model\UserMother;
use App\Tests\Domain\Travel\ValueObject\GeoLocationStub;
use App\Tests\Infrastructure\TravelBundle\Repository\InMemoryElasticSearchRepository;
use App\Tests\Infrastructure\TravelBundle\Repository\InMemoryTravelRepository;
use PHPUnit\Framework\TestCase;

class PopulateAndQueryIntegrationTest extends TestCase
{
    private InMemoryTravelRepository $travelRepository;
    private InMemoryElasticSearchRepository $elasticRepository;

    protected function setUp(): void
    {
        $this->travelRepository  = new InMemoryTravelRepository();
        $this->elasticRepository = new InMemoryElasticSearchRepository();
    }

    private function createPublishedTravel(string $title, int $stars): Travel
    {
        $travel = TravelMother::random();
        $travel->setTitle($title);
        $travel->setStars($stars);
        $travel->publish();
        return $travel;
    }

    private function createDraftTravel(string $title, int $stars): Travel
    {
        $travel = TravelMother::random();
        $travel->setTitle($title);
        $travel->setStars($stars);
        return $travel;
    }

    public function testPopulateIndexesAllTravelsFromRepository(): void
    {
        $this->travelRepository->save($this->createPublishedTravel('Toscana', 5));
        $this->travelRepository->save($this->createDraftTravel('Draft trip', 3));

        $populator = new PopulateIndexer($this->travelRepository, $this->elasticRepository);
        $populator->execute();

        $this->assertCount(2, $this->elasticRepository->getDocuments());
    }

    public function testGetBestTravelsOnlyReturnsPublished(): void
    {
        $published1 = $this->createPublishedTravel('Toscana', 5);
        $published2 = $this->createPublishedTravel('Creta', 3);
        $draft      = $this->createDraftTravel('Draft trip', 10);

        $this->elasticRepository->save($published1);
        $this->elasticRepository->save($published2);
        $this->elasticRepository->save($draft);

        $service = new GetBestTravelsOrderedByService($this->elasticRepository);
        $results = $service(new BestTravelsListQuery(10, 'stars'));

        $this->assertCount(2, $results);
        foreach ($results as $result) {
            $this->assertSame(Travel::TRAVEL_PUBLISHED, $result['status']);
        }
    }

    public function testGetBestTravelsRespectsMaxResults(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            $this->elasticRepository->save($this->createPublishedTravel("Travel $i", $i));
        }

        $service = new GetBestTravelsOrderedByService($this->elasticRepository);
        $results = $service(new BestTravelsListQuery(10, 'stars'));

        $this->assertCount(10, $results);
    }

    public function testGetBestTravelsOrderedByStarsDesc(): void
    {
        $this->elasticRepository->save($this->createPublishedTravel('Low',    2));
        $this->elasticRepository->save($this->createPublishedTravel('High',   5));
        $this->elasticRepository->save($this->createPublishedTravel('Medium', 3));

        $service = new GetBestTravelsOrderedByService($this->elasticRepository);
        $results = $service(new BestTravelsListQuery(10, 'stars'));

        $this->assertSame('High',   $results[0]['title']);
        $this->assertSame('Medium', $results[1]['title']);
        $this->assertSame('Low',    $results[2]['title']);
    }

    public function testGetBestTravelsReturnsEmptyWhenNonePublished(): void
    {
        $this->elasticRepository->save($this->createDraftTravel('Draft 1', 5));
        $this->elasticRepository->save($this->createDraftTravel('Draft 2', 3));

        $service = new GetBestTravelsOrderedByService($this->elasticRepository);
        $results = $service(new BestTravelsListQuery(10, 'stars'));

        $this->assertEmpty($results);
    }

    public function testPopulateThenQueryReturnsOnlyPublished(): void
    {
        $this->travelRepository->save($this->createPublishedTravel('Toscana', 5));
        $this->travelRepository->save($this->createPublishedTravel('Creta', 4));
        $this->travelRepository->save($this->createDraftTravel('Draft trip', 9));

        $populator = new PopulateIndexer($this->travelRepository, $this->elasticRepository);
        $populator->execute();

        $service = new GetBestTravelsOrderedByService($this->elasticRepository);
        $results = $service(new BestTravelsListQuery(10, 'stars'));

        $this->assertCount(2, $results);
        $this->assertSame('Toscana', $results[0]['title']);
        $this->assertSame('Creta',   $results[1]['title']);
    }
}
