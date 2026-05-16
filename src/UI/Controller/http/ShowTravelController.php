<?php

namespace App\UI\Controller\http;

use App\Application\Query\Travel\ShowTravelBySlugQuery;
use App\Domain\Travel\Repository\TravelRepository;
use App\Domain\TravelClone\Repository\TravelCloneRepository;
use App\Domain\User\Model\User;
use App\Infrastructure\Application\QueryBus\QueryBus;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Attribute\Route;

class ShowTravelController extends QueryController
{
    public function __construct(
        QueryBus $queryBus,
        Security $security,
        private TravelCloneRepository $travelCloneRepository,
        private TravelRepository $travelRepository
    ) {
        parent::__construct($queryBus, $security);
    }

    #[Route('/{_locale}/travel/{slug}', name: 'show_travel')]
    public function showTravel(string $slug)
    {
        /** @var User|null $currentUser */
        $currentUser = $this->security->getUser();
        $userId = $currentUser ? $currentUser->getId()->id() : null;

        $query = new ShowTravelBySlugQuery($slug, $userId);
        $travel = $this->ask($query);

        $cloneOrigin = null;
        $originalTravel = null;
        $cloneRecord = $this->travelCloneRepository->findByClonedTravelId($travel->getId()->id());

        if ($cloneRecord) {
            $cloneOrigin = $cloneRecord;
            try {
                $originalTravel = $this->travelRepository->ofIdOrFail($cloneRecord->getOriginalTravelId());
            } catch (\Exception $e) {
                $originalTravel = null;
            }
        }

        return $this->render(
            'travel/showTravel.html.twig',
            ['travel' => $travel, 'cloneOrigin' => $cloneOrigin, 'originalTravel' => $originalTravel]
        );
    }
}
