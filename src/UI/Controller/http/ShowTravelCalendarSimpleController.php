<?php

namespace App\UI\Controller\http;

use App\Application\Query\Travel\ShowTravelBySlugQuery;
use App\Domain\User\Model\User;
use Symfony\Component\Routing\Attribute\Route;

class ShowTravelCalendarSimpleController extends QueryController
{
    #[Route('/{_locale}/travel/{slug}/calendar-view', name: 'show_travel_calendar_simple')]
    public function show(string $slug)
    {
        /** @var User|null $currentUser */
        $currentUser = $this->security->getUser();
        $userId = $currentUser ? $currentUser->getId()->id() : null;

        $query = new ShowTravelBySlugQuery($slug, $userId);
        $travel = $this->ask($query);

        return $this->render('travel/calendarSimple.html.twig', ['travel' => $travel]);
    }
}
