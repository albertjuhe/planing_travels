<?php

namespace App\UI\Controller\http;

use App\Application\Query\Travel\ShowTravelBySlugQuery;
use App\Domain\User\Model\User;
use Symfony\Component\Routing\Annotation\Route;

class ShowTravelController extends QueryController
{
    /**
     * @param string $slug
     * @Route("/{_locale}/travel/{slug}",name="show_travel")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showTravel(string $slug)
    {
        /** @var User|null $currentUser */
        $currentUser = $this->security->getUser();
        $userId = $currentUser ? $currentUser->getId()->id() : null;

        $query = new ShowTravelBySlugQuery($slug, $userId);
        $travel = $this->ask($query);

        return $this->render(
            'travel/showTravel.html.twig',
            ['travel' => $travel]
        );
    }
}
