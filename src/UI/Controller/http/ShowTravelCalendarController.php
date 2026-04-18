<?php

namespace App\UI\Controller\http;

use App\Application\Query\Travel\ShowTravelBySlugQuery;
use App\Domain\User\Model\User;
use Symfony\Component\Routing\Annotation\Route;

class ShowTravelCalendarController extends QueryController
{
    /**
     * @Route("/{_locale}/travel/{slug}/calendar", name="show_travel_calendar")
     */
    public function showTravelCalendar(string $slug)
    {
        /** @var User|null $currentUser */
        $currentUser = $this->security->getUser();
        $userId = $currentUser ? $currentUser->getId()->id() : null;

        $query = new ShowTravelBySlugQuery($slug, $userId);
        $travel = $this->ask($query);

        return $this->render(
            'travel/calendarRoute.html.twig',
            ['travel' => $travel]
        );
    }
}
