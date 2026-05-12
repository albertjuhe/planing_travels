<?php

namespace App\UI\Controller\API;

use App\Application\Command\Travel\CloneTravelCommand;
use App\Application\Service\TravelAuthorizationService;
use App\Infrastructure\TravelBundle\Repository\DoctrineTravelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CloneTravelAPIController extends AbstractController
{
    private DoctrineTravelRepository $travelRepository;
    private MessageBusInterface $commandBus;
    private Security $security;
    private TravelAuthorizationService $authService;
    private UrlGeneratorInterface $router;

    public function __construct(
        DoctrineTravelRepository $travelRepository,
        MessageBusInterface $commandBus,
        Security $security,
        TravelAuthorizationService $authService,
        UrlGeneratorInterface $router
    ) {
        $this->travelRepository = $travelRepository;
        $this->commandBus = $commandBus;
        $this->security = $security;
        $this->authService = $authService;
        $this->router = $router;
    }

    #[Route('/api/travel/{travelId}/clone', name: 'api_clone_travel', methods: ['POST'])]
    public function clone(Request $request, string $travelId): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        try {
            $travel = $this->travelRepository->ofIdOrFail($travelId);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Travel not found'], 404);
        }

        if (!$this->authService->canClone($travel, $user)) {
            return new JsonResponse(['error' => 'Not allowed to clone this travel. It must be published or shared with you.'], 403);
        }

        $data = json_decode($request->getContent(), true) ?? [];
        $newTitle = !empty($data['title']) ? trim($data['title']) : null;
        $copyGpx = isset($data['copyGpx']) ? (bool) $data['copyGpx'] : true;

        $command = new CloneTravelCommand(
            $travelId,
            $user->getId()->id(),
            $newTitle,
            $copyGpx
        );

        $envelope = $this->commandBus->dispatch($command);
        $clonedTravel = $envelope->last(HandledStamp::class)->getResult();

        $redirectUrl = $this->router->generate('showTravel', [
            '_locale' => $user->getLocale() ?? 'en',
            'travelSlug' => $clonedTravel->getSlug(),
        ]);

        return new JsonResponse([
            'id' => $clonedTravel->getId()->id(),
            'slug' => $clonedTravel->getSlug(),
            'title' => $clonedTravel->getTitle(),
            'redirectUrl' => $redirectUrl,
        ], 201);
    }

    #[Route('/api/travel/{travelId}/lineage', name: 'api_travel_lineage', methods: ['GET'])]
    public function lineage(string $travelId): JsonResponse
    {
        try {
            $travel = $this->travelRepository->ofIdOrFail($travelId);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Travel not found'], 404);
        }

        $lineage = [];
        $current = $travel;
        $visited = [];
        $maxDepth = 10;

        while ($current !== null && $maxDepth-- > 0) {
            $id = $current->getId()->id();
            if (in_array($id, $visited, true)) {
                break;
            }
            array_unshift($lineage, [
                'id' => $id,
                'title' => $current->getTitle(),
                'ownerUsername' => $current->getUser()->getUsername(),
                'cloneCount' => $current->getCloneCount(),
                'clonedFromTitle' => $current->getClonedFromTitle(),
            ]);
            $visited[] = $id;

            $parentId = $current->getClonedFromTravelId();
            if ($parentId === null) {
                break;
            }
            try {
                $current = $this->travelRepository->ofIdOrFail($parentId);
            } catch (\Exception $e) {
                break;
            }
        }

        return new JsonResponse(['lineage' => $lineage]);
    }
}
