<?php

namespace App\UI\Controller\API;

use App\Application\Command\Location\AddLocationCommand;
use App\Domain\TypeLocation\Repository\TypeLocationRepository;
use App\UI\Controller\http\CommandController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\SecurityBundle\Security;

class AddNewLocationAPIController extends CommandController
{
    public function __construct(
        MessageBusInterface $commandBus,
        private readonly Security $security,
        private readonly TypeLocationRepository $typeLocationRepository,
    ) {
        parent::__construct($commandBus);
    }

    #[Route('/api/user/{userId}/location', name: 'newAPILocation', methods: ['POST'])]
    public function newLocation(Request $request, $userId): JsonResponse
    {
        $user = $this->security->getUser();
        if (empty($user) || $userId != $user->getId()->id()) {
            return new JsonResponse(['error' => 'Operation not allowed']);
        }

        $data = json_decode($request->getContent(), true);

        $idType = $data['IdType'];
        if (!is_numeric($idType)) {
            $typeLocation = $this->typeLocationRepository->idOrFail($idType);
            $idType = $typeLocation->getId();
        }

        $command = new AddLocationCommand(
            travelId: $data['travel'],
            userId: (int) $userId,
            title: $data['placeAddress'],
            address: $data['address'] ?? $data['placeAddress'],
            description: $data['comment'] ?? '',
            link: $data['link'] ?? '',
            latitude: (float) ($data['latitude'] ?? 0),
            longitude: (float) ($data['longitude'] ?? 0),
            placeId: $data['place_id'],
            locationType: (int) $idType,
        );

        $this->commandBus->dispatch($command);

        return new JsonResponse(['id' => $command->getLocationId()], Response::HTTP_CREATED);
    }
}
