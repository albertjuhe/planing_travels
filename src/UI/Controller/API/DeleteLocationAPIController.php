<?php

namespace App\UI\Controller\API;

use App\Application\Command\Location\DeleteLocationCommand;
use App\UI\Controller\http\BaseController;
use League\Tactician\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\Security;

class DeleteLocationAPIController extends BaseController
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
     * @Route("/api/location/{location}",name="deleteAPILocation")
     * @Method({"DELETE"})
     */
    public function deleteLocation(Request $request, $location)
    {
        $user = $this->security->getUser();
        if (empty($user)) {
            return new JsonResponse(
                $response['error'] = 'Operation not allowed'
            );
        }

        $deleteLocationCommand = new DeleteLocationCommand($location);
        $this->commandBus->handle($deleteLocationCommand);

        return new JsonResponse(
            $response['data'] = ['location' => $location]
        );
    }
}
