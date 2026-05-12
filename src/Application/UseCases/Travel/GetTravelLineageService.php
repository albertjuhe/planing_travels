<?php

namespace App\Application\UseCases\Travel;

use App\Application\Query\Travel\GetTravelLineageQuery;
use App\Application\UseCases\UsesCasesService;
use App\Domain\Travel\Repository\TravelRepository;

class GetTravelLineageService implements UsesCasesService
{
    private TravelRepository $travelRepository;

    public function __construct(TravelRepository $travelRepository)
    {
        $this->travelRepository = $travelRepository;
    }

    /**
     * Returns the chain of travels from root to the given travel.
     * Each item: ['id', 'title', 'clonedFromTitle', 'clonedFromTravelId', 'ownerUsername']
     */
    public function __invoke(GetTravelLineageQuery $query): array
    {
        $travel = $this->travelRepository->ofIdOrFail($query->getTravelId());

        $lineage = [];
        $lineage[] = [
            'id' => $travel->getId()->id(),
            'title' => $travel->getTitle(),
            'clonedFromTravelId' => $travel->getClonedFromTravelId(),
            'clonedFromTitle' => $travel->getClonedFromTitle(),
            'ownerUsername' => $travel->getUser()->getUsername(),
            'cloneCount' => $travel->getCloneCount(),
        ];

        $parentId = $travel->getClonedFromTravelId();
        $visited = [$travel->getId()->id()];
        $maxDepth = 10;

        while ($parentId !== null && $maxDepth-- > 0) {
            if (in_array($parentId, $visited, true)) {
                break;
            }
            try {
                $parent = $this->travelRepository->ofIdOrFail($parentId);
                array_unshift($lineage, [
                    'id' => $parent->getId()->id(),
                    'title' => $parent->getTitle(),
                    'clonedFromTravelId' => $parent->getClonedFromTravelId(),
                    'clonedFromTitle' => $parent->getClonedFromTitle(),
                    'ownerUsername' => $parent->getUser()->getUsername(),
                    'cloneCount' => $parent->getCloneCount(),
                ]);
                $visited[] = $parentId;
                $parentId = $parent->getClonedFromTravelId();
            } catch (\Exception $e) {
                break;
            }
        }

        return $lineage;
    }
}
