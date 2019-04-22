<?php

namespace App\UI\Controller\API;

use App\Application\Command\Location\AddLocationCommand;
use App\Domain\Mark\Model\Mark;
use App\Domain\Travel\ValueObject\GeoLocation;
use App\Domain\Location\Model\Location;
use App\UI\Controller\http\CommandController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use League\Tactician\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;

class AddNewLocationAPIController extends CommandController
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(CommandBus $commandBus, Security $security)
    {
        parent::__construct($commandBus);
        $this->security = $security;
    }

    /**
     * @Route("/api/user/{userId}/location",name="newAPILocation")
     * @Method({"POST"})
     */
    public function newLocation(Request $request, $userId)
    {
        $user = $this->security->getUser();
        if (empty($user) || $userId != $user->getId()->id()) {
            return new JsonResponse(
                $response['error'] = 'Operation not allowed'
            );
        }

        $data = json_decode($request->getContent(), true);
        $location = Location::fromArray($data);

        $geolocation = new GeoLocation($data['latitude'], $data['longitude'], 0, 0, 0, 0);
        $mark = Mark::fromGeolocationAndId($geolocation, $data['place_id']);
        $mark->setJson($request->getContent());
        $mark->setTitle($data['address']);

        $location->setMark($mark);

        $addLocationCommand = new AddLocationCommand($data['travel'], $location, $userId, $mark, $data['IdType']);
        $this->commandBus->handle($addLocationCommand);

        return new JsonResponse(
            $response['data'] = $data
        );
    }
}
