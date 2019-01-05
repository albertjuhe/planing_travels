<?php
/**
 * Created by PhpStorm.
 * User: ajuhe
 * Date: 5/01/19
 * Time: 18:03
 */

namespace App\UI\Controller\API;

use App\Application\Command\Travel\AddTravelCommand;
use App\Domain\Travel\Model\Travel;
use App\Infrastructure\TravelBundle\Form\TravelType;
use App\UI\Controller\http\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use App\Domain\User\Exceptions\UserDoesntExists;
use League\Tactician\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;

class AddNewTravelAPIController extends BaseController
{

    /**
     * ShowMyTravelsController constructor.
     * @param $commandBus
     */
    public function __construct(CommandBus $commandBus)
    {
        parent::__construct($commandBus);
    }

    /**
     * @Route("/api/travel",name="newAPITravel")
     * @Method({"POST"})
     */
    public function newTravel()
    {
      return new JsonResponse(array('data' => 123));
    }
}