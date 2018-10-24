<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 24/10/2018
 * Time: 19:14
 */

namespace App\UI\Controller;

use App\Application\Command\Travel\PublishTravelCommand;
use App\Domain\Travel\Model\Travel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Domain\User\Exceptions\UserDoesntExists;
use App\Application\Command\CommandBus;

class PublishTravelController extends BaseController
{

    /**
     * ShowMyTravelsController constructor.
     * @param $travelRepository
     * @param $commandBus
     */
    public function __construct(CommandBus $commandBus)
    {
        parent::__construct($commandBus);
    }

    /**
     * @Route("/{_locale}/private/publish/{slug}",name="publishTravel")
     * @param Request $request
     * @param $_locale
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws UserDoesntExists
     */
    public function publishTravel(Request $request, $_locale, string $slug)
    {
        if (!$this->getUser())
            throw new UserDoesntExists();

        $publishTravelCommand = new PublishTravelCommand($slug, $this->getUser());
        $this->commandBus->handle($publishTravelCommand);

        return $this->redirectToRoute('main_private');
    }
}