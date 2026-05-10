<?php

namespace App\UI\Controller\http;

use App\Application\Query\Travel\ShowTravelBySlugQuery;
use App\Domain\Budget\Model\TravelBudget;
use App\Domain\Budget\Model\TravelExpense;
use App\Domain\User\Model\User;
use App\Infrastructure\Application\QueryBus\QueryBus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;

class PrintTravelController extends QueryController
{
    private $em;

    public function __construct(QueryBus $queryBus, Security $security, EntityManagerInterface $em)
    {
        parent::__construct($queryBus, $security);
        $this->em = $em;
    }

    #[Route('/{_locale}/travel/{slug}/print', name: 'print_travel')]
    public function print(string $slug)
    {
        /** @var User|null $currentUser */
        $currentUser = $this->security->getUser();
        $userId = $currentUser ? $currentUser->getId()->id() : null;

        $query = new ShowTravelBySlugQuery($slug, $userId);
        $travel = $this->ask($query);

        $budget   = $this->em->getRepository(TravelBudget::class)->findOneBy(['travel' => $travel]);
        $expenses = $this->em->getRepository(TravelExpense::class)->findBy(
            ['travel' => $travel],
            ['expenseDate' => 'ASC', 'id' => 'ASC']
        );

        $totalSpent = 0.0;
        $totalByCategory = [];
        foreach ($expenses as $expense) {
            $totalSpent += (float) $expense->getAmount();
            $cat = $expense->getCategory();
            $totalByCategory[$cat] = ($totalByCategory[$cat] ?? 0.0) + (float) $expense->getAmount();
        }

        return $this->render('travel/printTravel.html.twig', [
            'travel'          => $travel,
            'budget'          => $budget,
            'expenses'        => $expenses,
            'totalSpent'      => $totalSpent,
            'totalByCategory' => $totalByCategory,
            'categories'      => TravelExpense::CATEGORIES,
        ]);
    }
}
