<?php

namespace App\UI\Controller\API;

use App\Application\Command\Location\DeleteLocationCommand;
use App\Domain\User\Model\User;
use App\UI\Controller\http\CommandController;
use League\Tactician\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\Security;

class DeleteLocationAPIController extends CommandController
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
     * @Route("/api/travel/{travel}/location/{location}",name="deleteAPILocation")
     * @Method({"DELETE"})
     */
    public function deleteLocation(Request $request, string $travel, string $location)
    {
        /** @var User $user */
        $user = $this->security->getUser();
        if (empty($user)) {
            return new JsonResponse(
                $response['error'] = 'Operation not allowed'
            );
        }

        $deleteLocationCommand = new DeleteLocationCommand($location, $travel, $user->userId());
        $this->commandBus->handle($deleteLocationCommand);

        return new JsonResponse(
            $response['data'] = [
                'location' => $location,
            ]
        );
    }
}
