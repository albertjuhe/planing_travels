<?php

namespace App\Tests\UI\API;

use App\Application\Service\TravelAuthorizationService;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;
use App\Infrastructure\JournalBundle\Repository\DoctrineJournalEntryRepository;
use App\Infrastructure\TravelBundle\Repository\DoctrineTravelRepository;
use App\Tests\Domain\Journal\Model\JournalEntryMother;
use App\Tests\Domain\Travel\Model\TravelMother;
use App\Tests\Domain\User\Model\UserMother;
use App\UI\Controller\API\JournalAPIController;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class JournalAPIControllerTest extends TestCase
{
    private function buildController(?User $user, Travel $travel, ?array $journalGrouped = []): JournalAPIController
    {
        $travelRepo = $this->createMock(DoctrineTravelRepository::class);
        $travelRepo->method('ofIdOrFail')->willReturn($travel);

        $journalRepo = $this->createMock(DoctrineJournalEntryRepository::class);
        $journalRepo->method('findByTravelGroupedByDate')->willReturn($journalGrouped ?? []);

        $commandBus = $this->createMock(MessageBusInterface::class);
        $commandBus->method('dispatch')->willReturnCallback(function ($command) {
            $stamp = new HandledStamp(null, 'handler');

            return new Envelope($command, [$stamp]);
        });

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $authService = $this->createMock(TravelAuthorizationService::class);
        $authService->method('canAccess')->willReturn($user !== null);

        $controller = new JournalAPIController($travelRepo, $journalRepo, $commandBus, $security, $authService);
        $controller->setContainer($this->createMock(\Symfony\Component\DependencyInjection\ContainerInterface::class));

        return $controller;
    }

    public function testListReturnsJournalGroupedByDate(): void
    {
        $user = UserMother::random();
        $travel = TravelMother::random();
        $travel->setUser($user);
        $entry = JournalEntryMother::forTravel($travel, $user, new \DateTime('2024-06-15'));

        $controller = $this->buildController($user, $travel, ['2024-06-15' => [$entry]]);

        $response = $controller->list($travel->getId()->id());

        $this->assertSame(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('journal', $data);
        $this->assertArrayHasKey('2024-06-15', $data['journal']);
    }

    public function testCreateWithoutAuthReturns401(): void
    {
        $travel = TravelMother::random();
        $travel->setUser(UserMother::random());

        $controller = $this->buildController(null, $travel);

        $request = new Request([], [], [], [], [], [], json_encode(['entryDate' => '2024-06-15', 'content' => 'Hello']));
        $response = $controller->create($request, $travel->getId()->id());

        $this->assertSame(401, $response->getStatusCode());
    }

    public function testCreateMissingFieldsReturns400(): void
    {
        $user = UserMother::random();
        $travel = TravelMother::random();
        $travel->setUser($user);

        $controller = $this->buildController($user, $travel);

        $request = new Request([], [], [], [], [], [], json_encode(['content' => 'Hello']));
        $response = $controller->create($request, $travel->getId()->id());

        $this->assertSame(400, $response->getStatusCode());
    }

    public function testDeleteWithoutAuthReturns401(): void
    {
        $travel = TravelMother::random();
        $travel->setUser(UserMother::random());

        $controller = $this->buildController(null, $travel);

        $response = $controller->delete('some-entry-id');

        $this->assertSame(401, $response->getStatusCode());
    }

    public function testUpdateWithoutAuthReturns401(): void
    {
        $travel = TravelMother::random();
        $travel->setUser(UserMother::random());

        $controller = $this->buildController(null, $travel);

        $request = new Request([], [], [], [], [], [], json_encode(['content' => 'x']));
        $response = $controller->update($request, 'some-id');

        $this->assertSame(401, $response->getStatusCode());
    }

    public function testVisibilityWithoutAuthReturns401(): void
    {
        $travel = TravelMother::random();
        $travel->setUser(UserMother::random());

        $controller = $this->buildController(null, $travel);

        $request = new Request([], [], [], [], [], [], json_encode(['isPublic' => true]));
        $response = $controller->visibility($request, 'some-id');

        $this->assertSame(401, $response->getStatusCode());
    }

    public function testUpdateMissingContentReturns400(): void
    {
        $user = UserMother::random();
        $travel = TravelMother::random();
        $travel->setUser($user);

        $controller = $this->buildController($user, $travel);

        $request = new Request([], [], [], [], [], [], json_encode(['title' => 'No content']));
        $response = $controller->update($request, 'some-id');

        $this->assertSame(400, $response->getStatusCode());
    }
}
