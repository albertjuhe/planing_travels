<?php

namespace App\UI\Controller\API;

use App\Application\Command\Location\AddLocationCommand;
use App\Domain\Mark\Model\Mark;
use App\Domain\Travel\ValueObject\GeoLocation;
use App\Domain\Location\Model\Location;
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
    private Security $security;
    private TypeLocationRepository $typeLocationRepository;

    public function __construct(MessageBusInterface $commandBus, Security $security, TypeLocationRepository $typeLocationRepository)
    {
        parent::__construct($commandBus);
        $this->security = $security;
        $this->typeLocationRepository = $typeLocationRepository;
    }

    #[Route('/api/user/{userId}/location', name: 'newAPILocation', methods: ['POST'])]
    public function newLocation(Request $request, $userId)
    {
        $user = $this->security->getUser();
        if (empty($user) || $userId != $user->getId()->id()) {
            return new JsonResponse(['error' => 'Operation not allowed']);
        }

        $data = json_decode($request->getContent(), true);
        $location = Location::fromArray($data);

        $geolocation = new GeoLocation($data['latitude'], $data['longitude'], 0, 0, 0, 0);
        $mark = Mark::fromGeolocationAndId($geolocation, $data['place_id']);
        $mark->setJson($request->getContent());
        $mark->setTitle($data['address']);

        $location->setMark($mark);

        $idType = $data['IdType'];
        if (!is_numeric($idType)) {
            $typeLocation = $this->typeLocationRepository->idOrFail($idType);
            $idType = $typeLocation->getId();
        }

        $addLocationCommand = new AddLocationCommand($data['travel'], $location, $userId, $mark, (int) $idType);
        $this->commandBus->dispatch($addLocationCommand);

        return new JsonResponse(['id' => $location->getId()->id()], Response::HTTP_CREATED);
    }
}
