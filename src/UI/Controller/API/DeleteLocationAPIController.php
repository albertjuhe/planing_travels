<?php

namespace App\UI\Controller\API;

use App\Application\Command\Location\DeleteLocationCommand;
use App\Domain\User\Model\User;
use App\UI\Controller\http\CommandController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;

class DeleteLocationAPIController extends CommandController
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(MessageBusInterface $commandBus, Security $security)
    {
        parent::__construct($commandBus);
        $this->security = $security;
    }

    #[Route('/api/travel/{travel}/location/{location}', name: 'deleteAPILocation', methods: ['DELETE'])]
    public function deleteLocation(Request $request, string $travel, string $location)
    {
        /** @var User $user */
        $user = $this->security->getUser();
        if (empty($user)) {
            return new JsonResponse(['error' => 'Operation not allowed']);
        }

        $deleteLocationCommand = new DeleteLocationCommand($location, $travel, $user->userId());
        $this->commandBus->dispatch($deleteLocationCommand);

        return new JsonResponse([
            'data' => [
                'location' => $location,
            ]
        ]);
    }
}
