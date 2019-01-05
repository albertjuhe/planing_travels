<?php
/**
 * Created by PhpStorm.
 * User: ajuhe
 * Date: 5/01/19
 * Time: 17:58
 */

namespace App\UI\Controller\API;

use App\UI\Controller\http\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Domain\User\Exceptions\UserDoesntExists;
use League\Tactician\CommandBus;

class AddNewLocationAPIController extends BaseController
{
    /**
     * ShowMyTravelsController constructor.
     * @param $commandBus
     */
    public function __construct(CommandBus $commandBus)
    {
        parent::__construct($commandBus);
    }

    public function newLocation() {

    }
}