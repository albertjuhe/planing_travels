<?php

namespace App\Tests\Application\UseCases\Budget;

use App\Application\Command\Budget\RegisterSettlementCommand;
use App\Application\Service\TravelAuthorizationService;
use App\Application\UseCases\Budget\RegisterSettlementService;
use App\Domain\Budget\Model\Settlement;
use App\Domain\User\Model\User;
use App\Infrastructure\TravelBundle\Repository\DoctrineTravelRepository;
use App\Infrastructure\UserBundle\Repository\DoctrineUserRepository;
use App\Tests\Domain\Travel\Model\TravelMother;
use App\Tests\Domain\User\Model\UserMother;
use App\Tests\Infrastructure\WebSocket\WebSocketNotifierSpy;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class RegisterSettlementServiceTest extends TestCase
{
    private function makeUserRepo(array $usersById): DoctrineUserRepository
    {
        $repo = $this->getMockBuilder(DoctrineUserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['ofIdOrFail'])
            ->getMock();
        $repo->method('ofIdOrFail')->willReturnCallback(function ($userId) use ($usersById) {
            $id = (string) (($userId instanceof \App\Domain\User\ValueObject\UserId) ? $userId->id() : $userId);
            return $usersById[$id] ?? array_values($usersById)[0];
        });

        return $repo;
    }
    private function buildService(User $fromUser, User $toUser, bool $canEdit = true): array
    {
        $travel = TravelMother::random();
        $travel->setUser($fromUser);

        $travelRepo = $this->createMock(DoctrineTravelRepository::class);
        $travelRepo->method('ofIdOrFail')->willReturn($travel);

        $usersById = [(string) $fromUser->getId()->id() => $fromUser, (string) $toUser->getId()->id() => $toUser];
        $userRepo = $this->makeUserRepo($usersById);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('persist');
        $em->method('flush');

        $authService = $this->createMock(TravelAuthorizationService::class);
        $authService->method('canEdit')->willReturn($canEdit);

        $notifier = new WebSocketNotifierSpy();

        $service = new RegisterSettlementService($travelRepo, $userRepo, $em, $authService, $notifier);

        return [$service, $travel, $notifier];
    }

    public function testHappyPathPersistsSettlementAndBroadcasts(): void
    {
        $fromUser = UserMother::random();
        $fromUser->setUsername('alice');
        $toUser = UserMother::random();
        $toUser->setUsername('bob');

        [$service, $travel, $notifier] = $this->buildService($fromUser, $toUser, true);

        $command = new RegisterSettlementCommand(
            $travel->getId()->id(),
            $fromUser->getId()->id(),
            $toUser->getId()->id(),
            47.50,
            'EUR',
            'Settling up'
        );

        $settlement = $service($command);

        $this->assertInstanceOf(Settlement::class, $settlement);
        $this->assertSame(47.50, $settlement->getAmount());
        $this->assertSame('EUR', $settlement->getCurrency());
        $this->assertCount(1, $notifier->broadcasts);
    }

    public function testWithoutPermissionThrowsRuntimeException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Not allowed to register a settlement for this travel.');

        $fromUser = UserMother::random();
        $toUser = UserMother::random();

        [$service, $travel] = $this->buildService($fromUser, $toUser, false);

        $command = new RegisterSettlementCommand(
            $travel->getId()->id(),
            $fromUser->getId()->id(),
            $toUser->getId()->id(),
            50.0,
            'EUR'
        );

        $service($command);
    }

    public function testBroadcastPayloadContainsRequiredFields(): void
    {
        $fromUser = UserMother::random();
        $fromUser->setUsername('charlie');
        $toUser = UserMother::random();
        $toUser->setUsername('diana');

        [$service, $travel, $notifier] = $this->buildService($fromUser, $toUser, true);

        $command = new RegisterSettlementCommand(
            $travel->getId()->id(),
            $fromUser->getId()->id(),
            $toUser->getId()->id(),
            25.0,
            'USD'
        );

        $service($command);

        $this->assertCount(1, $notifier->broadcasts);
        $broadcast = $notifier->broadcasts[0];

        $this->assertSame($travel->getId()->id(), $broadcast['travelId']);
        $payload = $broadcast['payload'];
        $this->assertSame('settlement.registered', $payload['type']);
        $this->assertSame($travel->getId()->id(), $payload['travelId']);
        $this->assertSame('charlie', $payload['fromUsername']);
        $this->assertSame('diana', $payload['toUsername']);
        $this->assertSame(25.0, $payload['amount']);
        $this->assertSame('USD', $payload['currency']);
    }
}
