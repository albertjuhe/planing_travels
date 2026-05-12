<?php

namespace App\Tests\Application\UseCases\Travel;

use App\Application\Command\Travel\CloneTravelCommand;
use App\Application\UseCases\Travel\CloneTravelService;
use App\Domain\Travel\Exceptions\TravelDoesntExists;
use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\Model\TravelClone;
use App\Domain\Travel\Repository\TravelRepository;
use App\Domain\User\Model\User;
use App\Domain\User\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class CloneTravelServiceTest extends TestCase
{
    private function buildPublishedTravel(User $owner): Travel
    {
        $travel = new Travel();
        $travel->setUser($owner);
        $travel->setTitle('Scotland Highlands');
        $travel->setDescription('A great trip');
        $travel->setStartAt(new \DateTime('2024-06-01'));
        $travel->setEndAt(new \DateTime('2024-06-10'));
        $travel->publish();

        return $travel;
    }

    public function testClonePublishedTravelCreatesNewTravelWithCorrectSnapshots(): void
    {
        $owner = User::byId(1);
        $cloner = User::byId(2);
        $source = $this->buildPublishedTravel($owner);

        $travelRepo = $this->createMock(TravelRepository::class);
        $travelRepo->method('ofIdOrFail')->willReturn($source);
        $travelRepo->method('save');

        $userRepo = $this->createMock(UserRepository::class);
        $userRepo->method('ofIdOrFail')->willReturn($cloner);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('persist');

        $service = new CloneTravelService($travelRepo, $userRepo, $em);
        $command = new CloneTravelCommand($source->getId()->id(), 2, null, false);

        $clone = $service($command);

        $this->assertSame('Scotland Highlands (copy)', $clone->getTitle());
        $this->assertSame($source->getId()->id(), $clone->getClonedFromTravelId());
        $this->assertSame($owner->getId()->id(), $clone->getClonedFromUserId());
        $this->assertSame('Scotland Highlands', $clone->getClonedFromTitle());
        $this->assertTrue($clone->isClone());
        $this->assertNotNull($clone->getClonedAt());
    }

    public function testCloneIncrementesSourceCloneCount(): void
    {
        $owner = User::byId(1);
        $cloner = User::byId(2);
        $source = $this->buildPublishedTravel($owner);

        $this->assertSame(0, $source->getCloneCount());

        $travelRepo = $this->createMock(TravelRepository::class);
        $travelRepo->method('ofIdOrFail')->willReturn($source);
        $travelRepo->method('save');

        $userRepo = $this->createMock(UserRepository::class);
        $userRepo->method('ofIdOrFail')->willReturn($cloner);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('persist');

        $service = new CloneTravelService($travelRepo, $userRepo, $em);
        $command = new CloneTravelCommand($source->getId()->id(), 2, 'My Custom Title', false);

        $clone = $service($command);

        $this->assertSame(1, $source->getCloneCount());
        $this->assertSame('My Custom Title', $clone->getTitle());
    }

    public function testCloneUnpublishedTravelByNonOwnerThrows(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Not allowed to clone this travel.');

        $owner = User::byId(1);
        $stranger = User::byId(99);

        $source = new Travel();
        $source->setUser($owner);
        $source->setTitle('Draft travel');
        $source->setStartAt(new \DateTime('2024-06-01'));
        $source->setEndAt(new \DateTime('2024-06-10'));

        $travelRepo = $this->createMock(TravelRepository::class);
        $travelRepo->method('ofIdOrFail')->willReturn($source);

        $userRepo = $this->createMock(UserRepository::class);
        $userRepo->method('ofIdOrFail')->willReturn($stranger);

        $em = $this->createMock(EntityManagerInterface::class);

        $service = new CloneTravelService($travelRepo, $userRepo, $em);
        $command = new CloneTravelCommand($source->getId()->id(), 99, null, false);
        $service($command);
    }

    public function testCloneDoesNotCopySharedUsersOrStatus(): void
    {
        $owner = User::byId(1);
        $sharedUser = User::byId(3);
        $cloner = User::byId(2);

        $source = $this->buildPublishedTravel($owner);
        $source->addShareduser($sharedUser);

        $travelRepo = $this->createMock(TravelRepository::class);
        $travelRepo->method('ofIdOrFail')->willReturn($source);
        $travelRepo->method('save');

        $userRepo = $this->createMock(UserRepository::class);
        $userRepo->method('ofIdOrFail')->willReturn($cloner);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('persist');

        $service = new CloneTravelService($travelRepo, $userRepo, $em);
        $command = new CloneTravelCommand($source->getId()->id(), 2, null, false);

        $clone = $service($command);

        $this->assertCount(0, $clone->getSharedusers());
        $this->assertFalse($clone->isPublished());
        $this->assertSame(Travel::TRAVEL_DRAFT, $clone->getStatus());
    }
}
