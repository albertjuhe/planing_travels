<?php

namespace App\UI\Controller\API;

use App\Domain\Budget\Model\TravelBudget;
use App\Domain\Budget\Model\TravelExpense;
use App\Infrastructure\TravelBundle\Repository\DoctrineTravelRepository;
use App\Infrastructure\LocationBundle\Repository\DoctrineLocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class BudgetAPIController extends AbstractController
{
    private $travelRepository;
    private $locationRepository;
    private $em;
    private $security;

    public function __construct(
        DoctrineTravelRepository $travelRepository,
        DoctrineLocationRepository $locationRepository,
        EntityManagerInterface $em,
        Security $security
    ) {
        $this->travelRepository = $travelRepository;
        $this->locationRepository = $locationRepository;
        $this->em = $em;
        $this->security = $security;
    }

    /**
     * @Route("/api/travel/{travelId}/budget", name="getBudget", methods={"GET"})
     */
    public function getBudget(string $travelId): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 403);
        }

        try {
            $travel = $this->travelRepository->ofIdOrFail($travelId);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Travel not found'], 404);
        }

        if (!$this->canAccess($travel, $user)) {
            return new JsonResponse(['error' => 'Forbidden'], 403);
        }

        $budget = $this->em->getRepository(TravelBudget::class)->findOneBy(['travel' => $travel]);
        $expenses = $this->em->getRepository(TravelExpense::class)->findBy(['travel' => $travel], ['expenseDate' => 'ASC', 'id' => 'ASC']);

        $totalByCategory = [];
        $totalSpent = 0.0;
        foreach ($expenses as $expense) {
            $cat = $expense->getCategory();
            $totalByCategory[$cat] = ($totalByCategory[$cat] ?? 0.0) + (float) $expense->getAmount();
            $totalSpent += (float) $expense->getAmount();
        }

        return new JsonResponse([
            'budget' => $budget ? [
                'id'       => $budget->getId(),
                'amount'   => (float) $budget->getAmount(),
                'currency' => $budget->getCurrency(),
            ] : null,
            'expenses' => array_map(function (TravelExpense $e) {
                return [
                    'id'          => $e->getId(),
                    'description' => $e->getDescription(),
                    'amount'      => (float) $e->getAmount(),
                    'currency'    => $e->getCurrency(),
                    'category'    => $e->getCategory(),
                    'expenseDate' => $e->getExpenseDate() ? $e->getExpenseDate()->format('Y-m-d') : null,
                    'locationId'  => $e->getLocation() ? $e->getLocation()->getId()->id() : null,
                    'locationTitle' => $e->getLocation() ? $e->getLocation()->getTitle() : null,
                ];
            }, $expenses),
            'totalSpent'      => $totalSpent,
            'totalByCategory' => $totalByCategory,
            'categories'      => TravelExpense::CATEGORIES,
        ]);
    }

    /**
     * @Route("/api/travel/{travelId}/budget", name="saveBudget", methods={"POST"})
     */
    public function saveBudget(Request $request, string $travelId): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 403);
        }

        try {
            $travel = $this->travelRepository->ofIdOrFail($travelId);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Travel not found'], 404);
        }

        if (!$this->canEdit($travel, $user)) {
            return new JsonResponse(['error' => 'Forbidden'], 403);
        }

        $data = json_decode($request->getContent(), true);
        $amount = (float) ($data['amount'] ?? 0);
        $currency = strtoupper($data['currency'] ?? 'EUR');

        $budget = $this->em->getRepository(TravelBudget::class)->findOneBy(['travel' => $travel]);
        if ($budget) {
            $budget->setAmount($amount);
            $budget->setCurrency($currency);
        } else {
            $budget = new TravelBudget($travel, $amount, $currency);
            $this->em->persist($budget);
        }
        $this->em->flush();

        return new JsonResponse([
            'id'       => $budget->getId(),
            'amount'   => (float) $budget->getAmount(),
            'currency' => $budget->getCurrency(),
        ]);
    }

    /**
     * @Route("/api/travel/{travelId}/expense", name="addExpense", methods={"POST"})
     */
    public function addExpense(Request $request, string $travelId): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 403);
        }

        try {
            $travel = $this->travelRepository->ofIdOrFail($travelId);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Travel not found'], 404);
        }

        if (!$this->canEdit($travel, $user)) {
            return new JsonResponse(['error' => 'Forbidden'], 403);
        }

        $data = json_decode($request->getContent(), true);

        if (empty($data['description']) || !isset($data['amount'])) {
            return new JsonResponse(['error' => 'description and amount are required'], 400);
        }

        $location = null;
        if (!empty($data['locationId'])) {
            try {
                $location = $this->locationRepository->findById($data['locationId']);
            } catch (\Exception $e) {
                // location optional, ignore
            }
        }

        $expenseDate = null;
        if (!empty($data['expenseDate'])) {
            $expenseDate = \DateTime::createFromFormat('Y-m-d', $data['expenseDate']) ?: null;
        }

        $expense = new TravelExpense(
            $travel,
            $data['description'],
            (float) $data['amount'],
            $data['category'] ?? TravelExpense::CATEGORY_OTHER,
            strtoupper($data['currency'] ?? 'EUR'),
            $location,
            $expenseDate
        );
        $this->em->persist($expense);
        $this->em->flush();

        return new JsonResponse([
            'id'          => $expense->getId(),
            'description' => $expense->getDescription(),
            'amount'      => (float) $expense->getAmount(),
            'currency'    => $expense->getCurrency(),
            'category'    => $expense->getCategory(),
            'expenseDate' => $expense->getExpenseDate() ? $expense->getExpenseDate()->format('Y-m-d') : null,
            'locationId'  => $expense->getLocation() ? $expense->getLocation()->getId()->id() : null,
            'locationTitle' => $expense->getLocation() ? $expense->getLocation()->getTitle() : null,
        ], 201);
    }

    /**
     * @Route("/api/expense/{expenseId}", name="deleteExpense", methods={"DELETE"})
     */
    public function deleteExpense(int $expenseId): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 403);
        }

        $expense = $this->em->getRepository(TravelExpense::class)->find($expenseId);
        if (!$expense) {
            return new JsonResponse(['error' => 'Expense not found'], 404);
        }

        if (!$this->canEdit($expense->getTravel(), $user)) {
            return new JsonResponse(['error' => 'Forbidden'], 403);
        }

        $this->em->remove($expense);
        $this->em->flush();

        return new JsonResponse(['success' => true]);
    }

    private function canAccess($travel, $user): bool
    {
        if ($travel->isPublished()) {
            return true;
        }

        return $this->canEdit($travel, $user);
    }

    private function canEdit($travel, $user): bool
    {
        if ($travel->getUser()->getId()->id() === $user->getId()->id()) {
            return true;
        }
        foreach ($travel->getSharedusers() as $shared) {
            if ($shared->getId()->id() === $user->getId()->id()) {
                return true;
            }
        }

        return false;
    }
}
