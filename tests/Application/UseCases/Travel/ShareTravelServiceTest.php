<?php

namespace App\Tests\Application\UseCases\Travel;

use App\Application\Command\Travel\ShareTravelCommand;
use App\Application\UseCases\Travel\ShareTravelService;
use App\Domain\Travel\Exceptions\InvalidTravelUser;
use App\Domain\User\Exceptions\UserDoesntExists;
use App\Tests\Domain\Travel\Model\TravelMother;
use App\Tests\Domain\User\Model\UserMother;
use App\Tests\Infrastructure\TravelBundle\Repository\InMemoryTravelRepository;

class ShareTravelServiceTest extends TravelService
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testShareTravelWithValidUser(): void
    {
        $owner = UserMother::random();
        $target = UserMother::random();

        $travel = TravelMother::random();
        $travel->setUser($owner);
        $this->travelRepository->save($travel);
        $this->userRepository->save($target);

        $command = new ShareTravelCommand(
            $travel->getId()->id(),
            (int) $owner->getId()->id(),
            $target->getUsername()
        );

        $service = new ShareTravelService($this->travelRepository, $this->userRepository);
        $service->handle($command);

        $sharedUsers = $travel->getSharedusers();
        $this->assertCount(1, $sharedUsers);
        $this->assertTrue($sharedUsers->first()->getId()->equalsTo($target->getId()));
    }

    public function testShareTravelThrowsWhenNotOwner(): void
    {
        $this->expectException(InvalidTravelUser::class);

        $owner = UserMother::random();
        $notOwner = UserMother::random();
        $target = UserMother::random();

        $travel = TravelMother::random();
        $travel->setUser($owner);
        $this->travelRepository->save($travel);
        $this->userRepository->save($target);

        $command = new ShareTravelCommand(
            $travel->getId()->id(),
            (int) $notOwner->getId()->id(),
            $target->getUsername()
        );

        $service = new ShareTravelService($this->travelRepository, $this->userRepository);
        $service->handle($command);
    }

    public function testShareTravelThrowsWhenTargetUserNotFound(): void
    {
        $this->expectException(UserDoesntExists::class);

        $owner = UserMother::random();
        $travel = TravelMother::random();
        $travel->setUser($owner);
        $this->travelRepository->save($travel);

        $command = new ShareTravelCommand(
            $travel->getId()->id(),
            (int) $owner->getId()->id(),
            'nonexistent_user'
        );

        $service = new ShareTravelService($this->travelRepository, $this->userRepository);
        $service->handle($command);
    }

    public function testShareTravelIsIdempotent(): void
    {
        $owner = UserMother::random();
        $target = UserMother::random();

        $travel = TravelMother::random();
        $travel->setUser($owner);
        $this->travelRepository->save($travel);
        $this->userRepository->save($target);

        $command = new ShareTravelCommand(
            $travel->getId()->id(),
            (int) $owner->getId()->id(),
            $target->getUsername()
        );

        $service = new ShareTravelService($this->travelRepository, $this->userRepository);
        $service->handle($command);
        $service->handle($command);

        $this->assertCount(1, $travel->getSharedusers());
    }
}
