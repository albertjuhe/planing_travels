<?php

namespace App\Tests\Application\UseCases\Travel;

use App\Application\Query\Travel\GetTravelLineageQuery;
use App\Application\UseCases\Travel\GetTravelLineageService;
use App\Domain\Travel\Repository\TravelRepository;
use App\Tests\Domain\Travel\Model\TravelMother;
use App\Tests\Domain\User\Model\UserMother;
use PHPUnit\Framework\TestCase;

class GetTravelLineageServiceTest extends TestCase
{
    public function testTravelWithoutParentReturnsOnlyItself(): void
    {
        $travel = TravelMother::random();
        $travel->setUser(UserMother::random());
        $travel->setTitle('Solo Trip');

        $travelRepo = $this->createMock(TravelRepository::class);
        $travelRepo->method('ofIdOrFail')->willReturn($travel);

        $service = new GetTravelLineageService($travelRepo);
        $query = new GetTravelLineageQuery($travel->getId()->id());

        $lineage = $service($query);

        $this->assertCount(1, $lineage);
        $this->assertSame($travel->getId()->id(), $lineage[0]['id']);
        $this->assertSame('Solo Trip', $lineage[0]['title']);
    }

    public function testTravelWithParentReturnsBothInOrder(): void
    {
        $owner = UserMother::random();
        $cloner = UserMother::random();

        $source = TravelMother::random();
        $source->setUser($owner);
        $source->setTitle('Original Trip');
        $source->publish();

        $clone = \App\Domain\Travel\Model\Travel::cloneFrom($source, $cloner, 'Clone Trip', false);
        $clone->setUser($cloner);

        $travelRepo = $this->createMock(TravelRepository::class);
        $travelRepo->method('ofIdOrFail')->willReturnCallback(function (string $id) use ($source, $clone) {
            if ($id === $clone->getId()->id()) {
                return $clone;
            }
            if ($id === $source->getId()->id()) {
                return $source;
            }
            throw new \RuntimeException('Not found');
        });

        $service = new GetTravelLineageService($travelRepo);
        $query = new GetTravelLineageQuery($clone->getId()->id());

        $lineage = $service($query);

        $this->assertCount(2, $lineage);
        $this->assertSame($source->getId()->id(), $lineage[0]['id']);
        $this->assertSame($clone->getId()->id(), $lineage[1]['id']);
    }

    public function testMissingParentInRepoStopsTraversalGracefully(): void
    {
        $owner = UserMother::random();
        $cloner = UserMother::random();

        $source = TravelMother::random();
        $source->setUser($owner);
        $source->publish();

        $clone = \App\Domain\Travel\Model\Travel::cloneFrom($source, $cloner, null, false);
        $clone->setUser($cloner);

        $travelRepo = $this->createMock(TravelRepository::class);
        $travelRepo->method('ofIdOrFail')->willReturnCallback(function (string $id) use ($clone) {
            if ($id === $clone->getId()->id()) {
                return $clone;
            }
            throw new \RuntimeException('Travel not found');
        });

        $service = new GetTravelLineageService($travelRepo);
        $query = new GetTravelLineageQuery($clone->getId()->id());

        $lineage = $service($query);

        $this->assertCount(1, $lineage);
        $this->assertSame($clone->getId()->id(), $lineage[0]['id']);
    }
}
