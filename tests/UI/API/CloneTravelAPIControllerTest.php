<?php

namespace App\Tests\UI\API;

use App\Application\Service\TravelAuthorizationService;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;
use App\Infrastructure\TravelBundle\Repository\DoctrineTravelRepository;
use App\Tests\Domain\Travel\Model\TravelMother;
use App\Tests\Domain\User\Model\UserMother;
use App\UI\Controller\API\CloneTravelAPIController;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CloneTravelAPIControllerTest extends TestCase
{
    private function buildController(?User $user, Travel $travel, bool $canClone = true): CloneTravelAPIController
    {
        $travelRepo = $this->createMock(DoctrineTravelRepository::class);
        $travelRepo->method('ofIdOrFail')->willReturn($travel);

        $clonedTravel = TravelMother::random();
        $clonedTravel->setUser($user ?? UserMother::random());
        $clonedTravel->setTitle('Clone (copy)');
        $clonedTravel->setSlug('clone-copy');

        $commandBus = $this->createMock(MessageBusInterface::class);
        $commandBus->method('dispatch')->willReturnCallback(function ($command) use ($clonedTravel) {
            return new Envelope($command, [new HandledStamp($clonedTravel, 'handler')]);
        });

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $authService = $this->createMock(TravelAuthorizationService::class);
        $authService->method('canClone')->willReturn($canClone);

        $router = $this->createMock(UrlGeneratorInterface::class);
        $router->method('generate')->willReturn('/en/travel/clone-copy');

        $controller = new CloneTravelAPIController($travelRepo, $commandBus, $security, $authService, $router);
        $controller->setContainer($this->createMock(\Symfony\Component\DependencyInjection\ContainerInterface::class));

        return $controller;
    }

    public function testCloneWithoutAuthReturns401(): void
    {
        $travel = TravelMother::random();
        $travel->setUser(UserMother::random());

        $controller = $this->buildController(null, $travel);

        $request = new Request([], [], [], [], [], [], json_encode(['title' => 'My Clone']));
        $response = $controller->clone($request, $travel->getId()->id());

        $this->assertSame(401, $response->getStatusCode());
    }

    public function testCloneNotAllowedReturns403(): void
    {
        $user = UserMother::random();
        $travel = TravelMother::random();
        $travel->setUser(UserMother::random());

        $controller = $this->buildController($user, $travel, false);

        $request = new Request([], [], [], [], [], [], json_encode([]));
        $response = $controller->clone($request, $travel->getId()->id());

        $this->assertSame(403, $response->getStatusCode());
    }

    public function testSuccessfulCloneReturns201WithTravelData(): void
    {
        $user = UserMother::random();
        $travel = TravelMother::random();
        $travel->setUser(UserMother::random());
        $travel->publish();

        $controller = $this->buildController($user, $travel, true);

        $request = new Request([], [], [], [], [], [], json_encode(['title' => 'My Clone', 'copyGpx' => true]));
        $response = $controller->clone($request, $travel->getId()->id());

        $this->assertSame(201, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('slug', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('redirectUrl', $data);
    }

    public function testLineageReturnsJsonWithLineageKey(): void
    {
        $user = UserMother::random();
        $travel = TravelMother::random();
        $travel->setUser($user);

        $controller = $this->buildController($user, $travel);

        $response = $controller->lineage($travel->getId()->id());

        $this->assertSame(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('lineage', $data);
    }
}
