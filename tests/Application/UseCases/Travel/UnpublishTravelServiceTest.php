<?php

namespace App\Tests\Application\UseCases\Travel;

use App\Application\Command\Travel\PublishTravelCommand;
use App\Application\Command\Travel\UnpublishTravelCommand;
use App\Application\UseCases\Travel\PublishTravelService;
use App\Application\UseCases\Travel\UnpublishTravelService;
use App\Domain\Travel\Exceptions\NotAllowedToPublishTravel;
use App\Domain\Travel\Model\Travel;
use App\Tests\Domain\User\Model\UserMother;
use App\Tests\Infrastructure\TravelBundle\Repository\InMemoryTravelRepository;

class UnpublishTravelServiceTest extends TravelService
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testUnpublishTravel(): void
    {
        $travel = $this->travelRepository->findTravelBySlug(InMemoryTravelRepository::TRAVEL_1);
        $user = $travel->getUser();

        $publishCommand = new PublishTravelCommand($travel->getSlug(), $user);
        $publishService = new PublishTravelService($this->travelRepository, $this->userRepository);
        $publishService->handle($publishCommand);

        $this->assertEquals(Travel::TRAVEL_PUBLISHED, $this->travelRepository->findTravelBySlug(InMemoryTravelRepository::TRAVEL_1)->getStatus());

        $unpublishCommand = new UnpublishTravelCommand($travel->getSlug(), $user);
        $unpublishService = new UnpublishTravelService($this->travelRepository, $this->userRepository);
        $unpublishService->handle($unpublishCommand);

        $result = $this->travelRepository->findTravelBySlug(InMemoryTravelRepository::TRAVEL_1);
        $this->assertEquals(Travel::TRAVEL_DRAFT, $result->getStatus());
        $this->assertNull($result->getPublishedAt());
    }

    public function testUnpublishNotAllowedException(): void
    {
        $this->expectException(NotAllowedToPublishTravel::class);

        $travel = $this->travelRepository->findTravelBySlug(InMemoryTravelRepository::TRAVEL_1);
        $user = $travel->getUser();

        $publishCommand = new PublishTravelCommand($travel->getSlug(), $user);
        $publishService = new PublishTravelService($this->travelRepository, $this->userRepository);
        $publishService->handle($publishCommand);

        $otherUser = UserMother::random();
        $unpublishCommand = new UnpublishTravelCommand($travel->getSlug(), $otherUser);
        $unpublishService = new UnpublishTravelService($this->travelRepository, $this->userRepository);
        $unpublishService->handle($unpublishCommand);
    }

    public function testUnpublishAlreadyDraftTravel(): void
    {
        $travel = $this->travelRepository->findTravelBySlug(InMemoryTravelRepository::TRAVEL_1);
        $user = $travel->getUser();

        $this->assertEquals(Travel::TRAVEL_DRAFT, $travel->getStatus());

        $unpublishCommand = new UnpublishTravelCommand($travel->getSlug(), $user);
        $unpublishService = new UnpublishTravelService($this->travelRepository, $this->userRepository);
        $unpublishService->handle($unpublishCommand);

        $result = $this->travelRepository->findTravelBySlug(InMemoryTravelRepository::TRAVEL_1);
        $this->assertEquals(Travel::TRAVEL_DRAFT, $result->getStatus());
    }
}
