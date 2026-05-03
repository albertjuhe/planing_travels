<?php

namespace App\Tests\Application\UseCases\Travel;

use App\Application\Command\Travel\ShareTravelCommand;
use App\Application\Command\Travel\UnshareTravelCommand;
use App\Application\UseCases\Travel\ShareTravelService;
use App\Application\UseCases\Travel\UnshareTravelService;
use App\Domain\Travel\Exceptions\InvalidTravelUser;
use App\Domain\User\Exceptions\UserDoesntExists;
use App\Tests\Domain\Travel\Model\TravelMother;
use App\Tests\Domain\User\Model\UserMother;

class UnshareTravelServiceTest extends TravelService
{
    public function setUp(): void
    {
        parent::setUp();
    }

    private function shareTravel($travel, $owner, $target): void
    {
        $this->userRepository->save($target);
        $command = new ShareTravelCommand(
            $travel->getId()->id(),
            (int) $owner->getId()->id(),
            $target->getUsername()
        );
        (new ShareTravelService($this->travelRepository, $this->userRepository))->handle($command);
    }

    public function testUnshareTravelRemovesUser(): void
    {
        $owner = UserMother::random();
        $target = UserMother::random();

        $travel = TravelMother::random();
        $travel->setUser($owner);
        $this->travelRepository->save($travel);

        $this->shareTravel($travel, $owner, $target);
        $this->assertCount(1, $travel->getSharedusers());

        $command = new UnshareTravelCommand(
            $travel->getId()->id(),
            (int) $owner->getId()->id(),
            $target->getUsername()
        );
        (new UnshareTravelService($this->travelRepository, $this->userRepository))->handle($command);

        $this->assertCount(0, $travel->getSharedusers());
    }

    public function testUnshareThrowsWhenNotOwner(): void
    {
        $this->expectException(InvalidTravelUser::class);

        $owner = UserMother::random();
        $notOwner = UserMother::random();
        $target = UserMother::random();

        $travel = TravelMother::random();
        $travel->setUser($owner);
        $this->travelRepository->save($travel);
        $this->shareTravel($travel, $owner, $target);

        $command = new UnshareTravelCommand(
            $travel->getId()->id(),
            (int) $notOwner->getId()->id(),
            $target->getUsername()
        );
        (new UnshareTravelService($this->travelRepository, $this->userRepository))->handle($command);
    }

    public function testUnshareThrowsWhenTargetUserNotFound(): void
    {
        $this->expectException(UserDoesntExists::class);

        $owner = UserMother::random();
        $travel = TravelMother::random();
        $travel->setUser($owner);
        $this->travelRepository->save($travel);

        $command = new UnshareTravelCommand(
            $travel->getId()->id(),
            (int) $owner->getId()->id(),
            'nonexistent_user'
        );
        (new UnshareTravelService($this->travelRepository, $this->userRepository))->handle($command);
    }

    public function testUnshareOnlyRemovesTargetUser(): void
    {
        $owner = UserMother::random();
        $target1 = UserMother::random();
        $target2 = UserMother::random();

        $travel = TravelMother::random();
        $travel->setUser($owner);
        $this->travelRepository->save($travel);

        $this->shareTravel($travel, $owner, $target1);
        $this->shareTravel($travel, $owner, $target2);
        $this->assertCount(2, $travel->getSharedusers());

        $command = new UnshareTravelCommand(
            $travel->getId()->id(),
            (int) $owner->getId()->id(),
            $target1->getUsername()
        );
        (new UnshareTravelService($this->travelRepository, $this->userRepository))->handle($command);

        $this->assertCount(1, $travel->getSharedusers());
        $this->assertTrue(
            $travel->getSharedusers()->first()->getId()->equalsTo($target2->getId())
        );
    }
}
